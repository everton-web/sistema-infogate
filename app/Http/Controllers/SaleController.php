<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class SaleController extends Controller
{
    public function index(Request $request): Response
    {
        $companyId = app('currentCompany')->id;

        $search = trim((string) $request->get('q'));
        $status = $request->get('status');

        $sales = Sale::query()
            ->where('company_id', $companyId)
            ->with(['customer:id,name'])
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

        return Inertia::render('Sales/Index', [
            'sales' => $sales,
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
            ->get(['id', 'name', 'sku', 'sale_price', 'unit', 'stock_quantity']);

        return Inertia::render('Sales/Create', [
            'customers' => $customers,
            'products' => $products,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $companyId = app('currentCompany')->id;

        $data = $request->validate([
            'customer_id' => ['nullable', Rule::exists('customers', 'id')->where('company_id', $companyId)],
            'payment_method' => ['nullable', Rule::in(['cash', 'credit_card', 'debit_card', 'pix', 'boleto', 'other'])],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'integer'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $sale = DB::transaction(function () use ($data, $companyId, $request) {
            $lastNumber = Sale::where('company_id', $companyId)->max('number') ?? 0;

            $sale = Sale::create([
                'company_id' => $companyId,
                'branch_id' => app()->bound('currentBranch') && app('currentBranch') ? app('currentBranch')->id : null,
                'customer_id' => $data['customer_id'] ?? null,
                'user_id' => $request->user()?->id,
                'number' => $lastNumber + 1,
                'status' => 'completed',
                'payment_method' => $data['payment_method'] ?? null,
                'subtotal' => 0,
                'discount' => $data['discount'] ?? 0,
                'total' => 0,
                'notes' => $data['notes'] ?? null,
            ]);

            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $itemDiscount = $item['discount'] ?? 0;
                $itemTotal = max(0, ($item['quantity'] * $item['unit_price']) - $itemDiscount);

                $sale->items()->create([
                    'product_id' => $item['product_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $itemDiscount,
                    'total' => $itemTotal,
                ]);

                $subtotal += $itemTotal;
            }

            $sale->update([
                'subtotal' => $subtotal,
                'total' => max(0, $subtotal - ($data['discount'] ?? 0)),
            ]);

            return $sale;
        });

        return redirect()
            ->route('sales.show', $sale)
            ->with('success', 'Venda #' . $sale->number . ' criada com sucesso.');
    }

    public function show(Sale $sale): Response
    {
        abort_unless((int) $sale->company_id === app('currentCompany')->id, 404);

        $sale->load(['customer', 'items.product']);

        return Inertia::render('Sales/Show', [
            'sale' => $sale,
        ]);
    }
}
