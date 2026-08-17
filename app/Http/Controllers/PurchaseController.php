<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PurchaseController extends Controller
{
    public function index(Request $request): Response
    {
        $companyId = app('currentCompany')->id;

        $search = trim((string) $request->get('q'));
        $status = $request->get('status');

        $purchases = Purchase::query()
            ->where('company_id', $companyId)
            ->with(['supplier:id,name'])
            ->when($search, function ($query) use ($search) {
                if (is_numeric($search)) {
                    $query->where('number', $search);
                } else {
                    $query->whereHas('supplier', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                        ->orWhere('invoice_number', 'like', "%{$search}%");
                }
            })
            ->when(
                in_array($status, ['draft', 'ordered', 'received', 'cancelled'], true),
                function ($query) use ($status) {
                    $query->where('status', $status);
                }
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Purchases/Index', [
            'purchases' => $purchases,
            'filters' => ['q' => $search, 'status' => $status],
        ]);
    }

    public function create(): Response
    {
        $companyId = app('currentCompany')->id;

        $suppliers = Supplier::where('company_id', $companyId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);

        $products = Product::where('company_id', $companyId)
            ->where('status', 'active')
            ->where('type', 'product')
            ->orderBy('name')
            ->get(['id', 'name', 'sku', 'cost_price', 'unit']);

        return Inertia::render('Purchases/Create', [
            'suppliers' => $suppliers,
            'products' => $products,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $companyId = app('currentCompany')->id;

        $data = $request->validate([
            'supplier_id' => ['nullable', Rule::exists('suppliers', 'id')->where('company_id', $companyId)],
            'invoice_number' => ['nullable', 'string', 'max:255'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'shipping' => ['nullable', 'numeric', 'min:0'],
            'expected_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'integer'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
        ]);

        $purchase = DB::transaction(function () use ($data, $companyId) {
            $lastNumber = Purchase::where('company_id', $companyId)->max('number') ?? 0;

            $purchase = Purchase::create([
                'company_id' => $companyId,
                'supplier_id' => $data['supplier_id'] ?? null,
                'number' => $lastNumber + 1,
                'invoice_number' => $data['invoice_number'] ?? null,
                'status' => 'draft',
                'subtotal' => 0,
                'discount' => $data['discount'] ?? 0,
                'shipping' => $data['shipping'] ?? 0,
                'total' => 0,
                'expected_date' => $data['expected_date'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $itemTotal = max(0, $item['quantity'] * $item['unit_cost']);

                $purchase->items()->create([
                    'product_id' => $item['product_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'total' => $itemTotal,
                ]);

                $subtotal += $itemTotal;
            }

            $purchase->update([
                'subtotal' => $subtotal,
                'total' => max(0, $subtotal - ($data['discount'] ?? 0) + ($data['shipping'] ?? 0)),
            ]);

            return $purchase;
        });

        return redirect()
            ->route('purchases.show', $purchase)
            ->with('success', 'Compra #' . $purchase->number . ' criada com sucesso.');
    }

    public function show(Purchase $purchase): Response
    {
        abort_unless((int) $purchase->company_id === app('currentCompany')->id, 404);

        $purchase->load(['supplier', 'items.product']);

        return Inertia::render('Purchases/Show', [
            'purchase' => $purchase,
        ]);
    }
}
