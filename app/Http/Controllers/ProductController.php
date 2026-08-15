<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $companyId = app('currentCompany')->id;

        $search = trim((string) $request->get('q'));
        $type = $request->get('type');
        $status = $request->get('status');

        $products = Product::query()
            ->where('company_id', $companyId)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%");
                });
            })
            ->when(in_array($type, ['product', 'service'], true), function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->when(in_array($status, ['active', 'inactive'], true), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('products.index', compact('products', 'search', 'type', 'status'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $companyId = app('currentCompany')->id;

        $data = $this->validatedData($request, $companyId);
        $data['company_id'] = $companyId;

        $product = Product::create($data);

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Produto/serviço cadastrado com sucesso.');
    }

    public function show(Product $product)
    {
        $this->ensureCompany($product);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $this->ensureCompany($product);

        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $this->ensureCompany($product);

        $companyId = app('currentCompany')->id;

        $data = $this->validatedData($request, $companyId, $product->id);

        $product->update($data);

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Produto/serviço atualizado com sucesso.');
    }

    private function validatedData(Request $request, int $companyId, ?int $ignoreId = null): array
    {
        $barcodeRule = Rule::unique('products', 'barcode')
            ->where(fn ($query) => $query->where('company_id', $companyId));

        if ($ignoreId) {
            $barcodeRule->ignore($ignoreId);
        }

        return $request->validate([
            'type' => ['required', Rule::in(['product', 'service'])],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:50'],
            'barcode' => ['nullable', 'string', 'max:50', $barcodeRule],
            'unit' => ['required', 'string', 'max:10'],
            'description' => ['nullable', 'string'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'stock_quantity' => ['nullable', 'numeric', 'min:0'],
            'stock_minimum' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);
    }

    private function ensureCompany(Product $product): void
    {
        abort_unless(
            (int) $product->company_id === (int) app('currentCompany')->id,
            404
        );
    }
}
