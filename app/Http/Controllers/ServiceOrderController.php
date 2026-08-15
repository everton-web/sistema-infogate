<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\ServiceOrder;
use App\Models\ServiceOrderItem;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceOrderController extends Controller
{
    public function index(Request $request)
    {
        $companyId = app('currentCompany')->id;

        $search = trim((string) $request->get('q'));
        $status = $request->get('status');

        $orders = ServiceOrder::query()
            ->with(['customer', 'vehicle'])
            ->where('company_id', $companyId)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('number', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn ($q) => $q->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($status && $status !== 'all', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest('opened_at')
            ->paginate(20)
            ->withQueryString();

        return view('service-orders.index', compact('orders', 'search', 'status'));
    }

    public function create(Request $request)
    {
        $companyId = app('currentCompany')->id;

        $customers = Customer::where('company_id', $companyId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $vehicles = collect();
        $selectedCustomer = $request->get('customer_id');

        if ($selectedCustomer) {
            $vehicles = Vehicle::where('company_id', $companyId)
                ->where('customer_id', $selectedCustomer)
                ->with(['brand', 'model'])
                ->get();
        }

        return view('service-orders.create', compact('customers', 'vehicles', 'selectedCustomer'));
    }

    public function store(Request $request)
    {
        $companyId = app('currentCompany')->id;
        $branchId = session('current_branch_id');

        $validated = $request->validate([
            'customer_id' => [
                'required',
                Rule::exists('customers', 'id')
                    ->where(fn ($q) => $q->where('company_id', $companyId)),
            ],
            'vehicle_id' => [
                'nullable',
                Rule::exists('vehicles', 'id')
                    ->where(fn ($q) => $q
                        ->where('company_id', $companyId)
                        ->where('customer_id', $request->input('customer_id'))),
            ],
            'priority' => ['required', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'complaint' => ['nullable', 'string'],
            'internal_notes' => ['nullable', 'string'],
        ]);

        $order = ServiceOrder::create([
            'company_id' => $companyId,
            'branch_id' => $branchId,
            'customer_id' => $validated['customer_id'],
            'vehicle_id' => $validated['vehicle_id'] ?? null,
            'number' => ServiceOrder::nextNumber($companyId),
            'status' => 'open',
            'priority' => $validated['priority'],
            'complaint' => $validated['complaint'] ?? null,
            'internal_notes' => $validated['internal_notes'] ?? null,
            'opened_at' => now(),
        ]);

        return redirect()
            ->route('service-orders.show', $order)
            ->with('success', 'Ordem de serviço #' . $order->number . ' criada com sucesso.');
    }

    public function show(ServiceOrder $serviceOrder)
    {
        $this->ensureCompany($serviceOrder);

        $serviceOrder->load(['customer', 'vehicle.brand', 'vehicle.model', 'items.product']);

        $products = Product::where('company_id', app('currentCompany')->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('service-orders.show', [
            'order' => $serviceOrder,
            'products' => $products,
        ]);
    }

    public function updateStatus(Request $request, ServiceOrder $serviceOrder)
    {
        $this->ensureCompany($serviceOrder);
        $this->ensureEditable($serviceOrder);

        $validated = $request->validate([
            'status' => ['required', Rule::in([
                'open', 'in_progress', 'waiting_parts',
                'waiting_approval', 'completed', 'delivered', 'cancelled',
            ])],
        ]);

        $now = now();
        $updates = ['status' => $validated['status']];

        match ($validated['status']) {
            'in_progress' => $updates['started_at'] = $serviceOrder->started_at ?? $now,
            'completed' => $updates['completed_at'] = $now,
            'delivered' => $updates['delivered_at'] = $now,
            'cancelled' => $updates['cancelled_at'] = $now,
            default => null,
        };

        $serviceOrder->update($updates);

        return redirect()
            ->route('service-orders.show', $serviceOrder)
            ->with('success', 'Status atualizado para: ' . $serviceOrder->statusLabel());
    }

    public function addItem(Request $request, ServiceOrder $serviceOrder)
    {
        $this->ensureCompany($serviceOrder);
        $this->ensureEditable($serviceOrder);

        $validated = $request->validate([
            'product_id' => [
                'nullable',
                Rule::exists('products', 'id')
                    ->where(fn ($q) => $q->where('company_id', app('currentCompany')->id)),
            ],
            'type' => ['required', Rule::in(['product', 'service'])],
            'description' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $discount = $validated['discount'] ?? 0;

        if (! empty($validated['product_id'])) {
            $validated['type'] = Product::where('company_id', app('currentCompany')->id)
                ->findOrFail($validated['product_id'])
                ->type;
        }

        $total = ($validated['quantity'] * $validated['unit_price']) - $discount;

        ServiceOrderItem::create([
            'service_order_id' => $serviceOrder->id,
            'product_id' => $validated['product_id'] ?? null,
            'type' => $validated['type'],
            'description' => $validated['description'],
            'quantity' => $validated['quantity'],
            'unit_price' => $validated['unit_price'],
            'discount' => $discount,
            'total' => max(0, $total),
        ]);

        $serviceOrder->recalculateTotals();

        return redirect()
            ->route('service-orders.show', $serviceOrder)
            ->with('success', 'Item adicionado à OS.');
    }

    public function removeItem(ServiceOrder $serviceOrder, ServiceOrderItem $item)
    {
        $this->ensureCompany($serviceOrder);
        $this->ensureEditable($serviceOrder);

        abort_unless((int) $item->service_order_id === (int) $serviceOrder->id, 404);

        $item->delete();
        $serviceOrder->recalculateTotals();

        return redirect()
            ->route('service-orders.show', $serviceOrder)
            ->with('success', 'Item removido da OS.');
    }

    public function customerVehicles(Customer $customer)
    {
        $companyId = app('currentCompany')->id;

        abort_unless((int) $customer->company_id === $companyId, 404);

        $vehicles = Vehicle::where('customer_id', $customer->id)
            ->where('company_id', $companyId)
            ->with(['brand', 'model'])
            ->get()
            ->map(fn ($v) => [
                'id' => $v->id,
                'label' => $v->plate . ' - ' . ($v->brand?->name ?? '') . ' ' . ($v->model?->name ?? ''),
            ]);

        return response()->json($vehicles);
    }

    private function ensureCompany(ServiceOrder $order): void
    {
        abort_unless(
            (int) $order->company_id === (int) app('currentCompany')->id,
            404
        );
    }

    private function ensureEditable(ServiceOrder $order): void
    {
        abort_if(
            $order->isFinalized(),
            422,
            'Ordens entregues ou canceladas não podem mais ser alteradas.'
        );
    }
}
