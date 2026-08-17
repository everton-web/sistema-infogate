<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\ServiceOrder;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ServiceOrderController extends Controller
{
    public function index(Request $request): Response
    {
        $companyId = app('currentCompany')->id;

        $search = trim((string) $request->get('q'));
        $status = $request->get('status');

        $orders = ServiceOrder::query()
            ->where('company_id', $companyId)
            ->with(['customer:id,name', 'vehicle:id,plate'])
            ->when($search, function ($query) use ($search) {
                if (is_numeric($search)) {
                    $query->where('number', $search);
                } else {
                    $query->whereHas('customer', fn ($q) => $q->where('name', 'like', "%{$search}%"));
                }
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('ServiceOrders/Index', [
            'orders' => $orders,
            'filters' => ['q' => $search, 'status' => $status],
        ]);
    }

    public function create(): Response
    {
        $companyId = app('currentCompany')->id;

        $customers = Customer::where('company_id', $companyId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);

        $products = Product::where('company_id', $companyId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'type', 'sale_price', 'unit']);

        return Inertia::render('ServiceOrders/Create', [
            'customers' => $customers,
            'products' => $products,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $companyId = app('currentCompany')->id;

        $data = $request->validate([
            'customer_id' => ['required', Rule::exists('customers', 'id')->where('company_id', $companyId)],
            'vehicle_id' => ['nullable', Rule::exists('vehicles', 'id')->where('company_id', $companyId)],
            'complaint' => ['nullable', 'string'],
            'diagnosis' => ['nullable', 'string'],
            'internal_notes' => ['nullable', 'string'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'items' => ['nullable', 'array'],
            'items.*.product_id' => ['nullable', 'integer'],
            'items.*.type' => ['required', Rule::in(['product', 'service'])],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $order = DB::transaction(function () use ($data, $companyId, $request) {
            $lastNumber = ServiceOrder::where('company_id', $companyId)->max('number') ?? 0;

            $order = ServiceOrder::create([
                'company_id' => $companyId,
                'branch_id' => app()->bound('currentBranch') && app('currentBranch') ? app('currentBranch')->id : null,
                'customer_id' => $data['customer_id'],
                'vehicle_id' => $data['vehicle_id'] ?? null,
                'number' => $lastNumber + 1,
                'status' => 'open',
                'complaint' => $data['complaint'] ?? null,
                'diagnosis' => $data['diagnosis'] ?? null,
                'internal_notes' => $data['internal_notes'] ?? null,
                'discount' => $data['discount'] ?? 0,
                'total' => 0,
                'opened_at' => now(),
            ]);

            $total = 0;
            foreach ($data['items'] ?? [] as $item) {
                $itemDiscount = $item['discount'] ?? 0;
                $itemTotal = ($item['quantity'] * $item['unit_price']) - $itemDiscount;

                $order->items()->create([
                    'product_id' => $item['product_id'] ?? null,
                    'type' => $item['type'],
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $itemDiscount,
                    'total' => max(0, $itemTotal),
                ]);

                $total += max(0, $itemTotal);
            }

            $order->update(['total' => max(0, $total - ($data['discount'] ?? 0))]);

            return $order;
        });

        return redirect()
            ->route('service-orders.show', $order)
            ->with('success', 'Ordem de Serviço #' . $order->number . ' criada com sucesso.');
    }

    public function show(ServiceOrder $serviceOrder): Response
    {
        abort_unless((int) $serviceOrder->company_id === app('currentCompany')->id, 404);

        $serviceOrder->load(['customer', 'vehicle.brand', 'vehicle.model', 'items.product']);

        return Inertia::render('ServiceOrders/Show', [
            'order' => $serviceOrder,
        ]);
    }

    public function customerVehicles(Customer $customer)
    {
        abort_unless((int) $customer->company_id === app('currentCompany')->id, 404);

        return response()->json(
            Vehicle::where('customer_id', $customer->id)
                ->with(['brand:id,name', 'model:id,name'])
                ->get(['id', 'plate', 'vehicle_brand_id', 'vehicle_model_id'])
                ->map(fn ($v) => [
                    'id' => $v->id,
                    'plate' => $v->plate,
                    'label' => $v->plate . ' - ' . ($v->brand?->name ?? '') . ' ' . ($v->model?->name ?? ''),
                ])
        );
    }
}
