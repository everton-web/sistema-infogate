<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class StockController extends Controller
{
    public function index(Request $request): Response
    {
        $companyId = app('currentCompany')->id;

        $search = trim((string) $request->get('q'));

        $products = Product::query()
            ->where('company_id', $companyId)
            ->where('type', 'product')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString()
            ->through(fn ($product) => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'stock_quantity' => $product->stock_quantity,
                'stock_minimum' => $product->stock_minimum,
                'status' => $product->status,
            ]);

        return Inertia::render('Stock/Index', [
            'products' => $products,
            'filters' => ['q' => $search],
        ]);
    }

    public function movements(Product $product): Response
    {
        abort_unless((int) $product->company_id === app('currentCompany')->id, 404);

        $movements = StockMovement::where('product_id', $product->id)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Stock/Movements', [
            'product' => $product,
            'movements' => $movements,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $companyId = app('currentCompany')->id;

        $data = $request->validate([
            'type' => ['required', Rule::in(['entry', 'exit', 'adjustment'])],
            'product_id' => ['required', Rule::exists('products', 'id')->where('company_id', $companyId)],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($data, $companyId, $request) {
            $product = Product::where('company_id', $companyId)->findOrFail($data['product_id']);

            StockMovement::create([
                'company_id' => $companyId,
                'product_id' => $product->id,
                'type' => $data['type'],
                'quantity' => $data['quantity'],
                'unit_cost' => $data['unit_cost'] ?? 0,
                'reason' => $data['reason'] ?? null,
                'user_id' => $request->user()?->id,
            ]);

            $currentQuantity = (float) $product->stock_quantity;
            $quantity = (float) $data['quantity'];

            $newQuantity = match ($data['type']) {
                'entry' => $currentQuantity + $quantity,
                'exit' => $currentQuantity - $quantity,
                'adjustment' => $quantity,
                default => $currentQuantity,
            };

            $product->update(['stock_quantity' => max(0, $newQuantity)]);
        });

        return redirect()
            ->route('stock.index')
            ->with('success', 'Movimentação de estoque registrada com sucesso.');
    }
}
