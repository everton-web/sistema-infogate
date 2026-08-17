<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $companyId = app('currentCompany')->id;

        $search = trim((string) $request->get('q'));
        $type = $request->get('type');

        $products = Product::query()
            ->where('company_id', $companyId)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->when(in_array($type, ['product', 'service'], true), function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'filters' => [
                'q' => $search,
                'type' => $type,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Products/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $companyId = app('currentCompany')->id;

        $data = $request->validate([
            'type' => ['required', Rule::in(['product', 'service'])],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:50', Rule::unique('products')->where('company_id', $companyId)],
            'barcode' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'unit' => ['required', 'string', 'max:10'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'stock_quantity' => ['nullable', 'numeric', 'min:0'],
            'stock_minimum' => ['nullable', 'numeric', 'min:0'],
            'category' => ['nullable', 'string', 'max:100'],
            'brand' => ['nullable', 'string', 'max:100'],
            'ncm' => ['nullable', 'string', 'max:10'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $data['company_id'] = $companyId;

        Product::create($data);

        return redirect()
            ->route('products.index')
            ->with('success', 'Produto cadastrado com sucesso.');
    }

    public function show(Product $product): Response
    {
        abort_unless((int) $product->company_id === app('currentCompany')->id, 404);

        return Inertia::render('Products/Show', [
            'product' => $product,
        ]);
    }

    public function edit(Product $product): Response
    {
        abort_unless((int) $product->company_id === app('currentCompany')->id, 404);

        return Inertia::render('Products/Edit', [
            'product' => $product,
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        abort_unless((int) $product->company_id === app('currentCompany')->id, 404);

        $companyId = app('currentCompany')->id;

        $data = $request->validate([
            'type' => ['required', Rule::in(['product', 'service'])],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:50', Rule::unique('products')->where('company_id', $companyId)->ignore($product->id)],
            'barcode' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'unit' => ['required', 'string', 'max:10'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'stock_quantity' => ['nullable', 'numeric', 'min:0'],
            'stock_minimum' => ['nullable', 'numeric', 'min:0'],
            'category' => ['nullable', 'string', 'max:100'],
            'brand' => ['nullable', 'string', 'max:100'],
            'ncm' => ['nullable', 'string', 'max:10'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $product->update($data);

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Produto atualizado com sucesso.');
    }
}
