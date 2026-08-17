<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Quote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class QuoteController extends Controller
{
    public function index(Request $request): Response
    {
        $companyId = app('currentCompany')->id;

        $search = trim((string) $request->get('q'));
        $status = $request->get('status');

        $quotes = Quote::query()
            ->where('company_id', $companyId)
            ->with(['customer:id,name', 'vehicle:id,plate'])
            ->when($search, function ($query) use ($search) {
                if (is_numeric($search)) {
                    $query->where('number', $search);
                } else {
                    $query->whereHas('customer', fn ($q) => $q->where('name', 'like', "%{$search}%"));
                }
            })
            ->when(
                in_array($status, ['draft', 'sent', 'approved', 'rejected', 'expired'], true),
                function ($query) use ($status) {
                    $query->where('status', $status);
                }
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Quotes/Index', [
            'quotes' => $quotes,
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

        return Inertia::render('Quotes/Create', [
            'customers' => $customers,
            'products' => $products,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $companyId = app('currentCompany')->id;

        $data = $request->validate([
            'customer_id' => ['nullable', Rule::exists('customers', 'id')->where('company_id', $companyId)],
            'vehicle_id' => ['nullable', Rule::exists('vehicles', 'id')->where('company_id', $companyId)],
            'valid_until' => ['nullable', 'date'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'integer'],
            'items.*.type' => ['required', Rule::in(['product', 'service'])],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $quote = DB::transaction(function () use ($data, $companyId) {
            $lastNumber = Quote::where('company_id', $companyId)->max('number') ?? 0;

            $quote = Quote::create([
                'company_id' => $companyId,
                'customer_id' => $data['customer_id'] ?? null,
                'vehicle_id' => $data['vehicle_id'] ?? null,
                'number' => $lastNumber + 1,
                'status' => 'draft',
                'valid_until' => $data['valid_until'] ?? null,
                'subtotal' => 0,
                'discount' => $data['discount'] ?? 0,
                'total' => 0,
                'notes' => $data['notes'] ?? null,
            ]);

            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $itemDiscount = $item['discount'] ?? 0;
                $itemTotal = max(0, ($item['quantity'] * $item['unit_price']) - $itemDiscount);

                $quote->items()->create([
                    'product_id' => $item['product_id'] ?? null,
                    'type' => $item['type'],
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $itemDiscount,
                    'total' => $itemTotal,
                ]);

                $subtotal += $itemTotal;
            }

            $quote->update([
                'subtotal' => $subtotal,
                'total' => max(0, $subtotal - ($data['discount'] ?? 0)),
            ]);

            return $quote;
        });

        return redirect()
            ->route('quotes.show', $quote)
            ->with('success', 'Orçamento #' . $quote->number . ' criado com sucesso.');
    }

    public function show(Quote $quote): Response
    {
        abort_unless((int) $quote->company_id === app('currentCompany')->id, 404);

        $quote->load(['customer', 'vehicle', 'items.product']);

        return Inertia::render('Quotes/Show', [
            'quote' => $quote,
        ]);
    }
}
