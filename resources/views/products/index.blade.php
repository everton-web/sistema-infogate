@extends('layouts.erp')

@section('title', 'Produtos e Serviços')

@push('head')
<link rel="stylesheet" href="{{ asset('assets/canalsom-modules.css') }}">
@endpush

@section('content')
<div class="module-heading">
    <div>
        <h1>Produtos e Serviços</h1>
        <p>Itens cadastrados para {{ $currentCompany->trade_name ?? $currentCompany->name }}.</p>
    </div>

    <div class="module-actions">
        <a href="{{ route('products.create') }}" class="module-btn module-btn-primary">
            + Novo item
        </a>
    </div>
</div>

@if(session('success'))
    <div class="module-alert module-alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="module-card">
    <div class="module-card-header">
        <div>
            <h2>Catálogo</h2>
        </div>

        <form method="GET" action="{{ route('products.index') }}" class="module-search-form">
            <input
                type="text"
                name="q"
                value="{{ $search ?? '' }}"
                placeholder="Buscar por nome, SKU ou código de barras..."
                class="module-search-input"
            >

            <select name="type" class="module-filter-select" onchange="this.form.submit()">
                <option value="">Todos os tipos</option>
                <option value="product" @selected(($type ?? '') === 'product')>Produtos</option>
                <option value="service" @selected(($type ?? '') === 'service')>Serviços</option>
            </select>

            <select name="status" class="module-filter-select" onchange="this.form.submit()">
                <option value="">Todas as situações</option>
                <option value="active" @selected(($status ?? '') === 'active')>Ativos</option>
                <option value="inactive" @selected(($status ?? '') === 'inactive')>Inativos</option>
            </select>

            <button type="submit" class="module-btn module-btn-light">Buscar</button>
        </form>
    </div>

    @if($products->count())
        <div class="module-table-scroll">
            <table class="module-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>SKU</th>
                        <th>Preço venda</th>
                        <th>Estoque</th>
                        <th class="cell-right">Situação</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($products as $product)
                        <tr class="clickable-row" onclick="window.location='{{ route('products.show', $product) }}'">
                            <td><strong>{{ $product->name }}</strong></td>
                            <td>{{ $product->type === 'service' ? 'Serviço' : 'Produto' }}</td>
                            <td>{{ $product->sku ?: '—' }}</td>
                            <td>R$ {{ number_format($product->sale_price, 2, ',', '.') }}</td>
                            <td>
                                @if($product->type === 'service')
                                    —
                                @else
                                    {{ number_format($product->stock_quantity, 0, ',', '.') }}
                                    @if($product->stock_quantity <= $product->stock_minimum && $product->stock_minimum > 0)
                                        <span class="status-chip status-inactive">Baixo</span>
                                    @endif
                                @endif
                            </td>
                            <td class="cell-right">
                                @if($product->status === 'inactive')
                                    <span class="status-chip status-inactive">Inativo</span>
                                @else
                                    <span class="status-chip status-active">Ativo</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="simple-pagination">
                @if($products->onFirstPage())
                    <span class="pagination-disabled">← Anterior</span>
                @else
                    <a href="{{ $products->previousPageUrl() }}">← Anterior</a>
                @endif

                <span>Página {{ $products->currentPage() }} de {{ $products->lastPage() }}</span>

                @if($products->hasMorePages())
                    <a href="{{ $products->nextPageUrl() }}">Próxima →</a>
                @else
                    <span class="pagination-disabled">Próxima →</span>
                @endif
            </div>
        @endif
    @else
        <div class="module-empty">
            <strong>Nenhum produto ou serviço cadastrado.</strong>
            <p>Cadastre o primeiro item para montar seu catálogo.</p>

            <a href="{{ route('products.create') }}" class="module-btn module-btn-primary">
                Cadastrar item
            </a>
        </div>
    @endif
</div>
@endsection
