@extends('layouts.erp')

@section('title', $product->name)

@push('head')
<link rel="stylesheet" href="{{ asset('assets/canalsom-modules.css') }}">
@endpush

@section('content')
<div class="module-heading">
    <div>
        <a href="{{ route('products.index') }}" class="module-back-link">← Voltar para produtos</a>
        <h1>{{ $product->name }}</h1>
        <p>{{ $product->type === 'service' ? 'Serviço' : 'Produto' }}{{ $product->sku ? ' · SKU: ' . $product->sku : '' }}</p>
    </div>

    <div class="module-actions">
        <a href="{{ route('products.edit', $product) }}" class="module-btn module-btn-primary">
            Editar item
        </a>
    </div>
</div>

@if(session('success'))
    <div class="module-alert module-alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="customer-profile-grid">
    <section class="module-card customer-profile-card">
        <div class="module-card-header">
            <h2>Dados do item</h2>
        </div>

        <div class="customer-details">
            <div>
                <span>Tipo</span>
                <strong>{{ $product->type === 'service' ? 'Serviço' : 'Produto' }}</strong>
            </div>

            <div>
                <span>Unidade</span>
                <strong>{{ $product->unit }}</strong>
            </div>

            <div>
                <span>SKU</span>
                <strong>{{ $product->sku ?: '—' }}</strong>
            </div>

            <div>
                <span>Código de barras</span>
                <strong>{{ $product->barcode ?: '—' }}</strong>
            </div>

            <div>
                <span>Situação</span>
                <strong>
                    @if($product->status === 'inactive')
                        <span class="status-chip status-inactive">Inativo</span>
                    @else
                        <span class="status-chip status-active">Ativo</span>
                    @endif
                </strong>
            </div>
        </div>
    </section>

    <section class="module-card customer-profile-card">
        <div class="module-card-header">
            <h2>Preços e estoque</h2>
        </div>

        <div class="customer-details">
            <div>
                <span>Preço de custo</span>
                <strong>R$ {{ number_format($product->cost_price, 2, ',', '.') }}</strong>
            </div>

            <div>
                <span>Preço de venda</span>
                <strong>R$ {{ number_format($product->sale_price, 2, ',', '.') }}</strong>
            </div>

            @if($product->type === 'product')
                <div>
                    <span>Estoque atual</span>
                    <strong>
                        {{ number_format($product->stock_quantity, 0, ',', '.') }}
                        @if($product->stock_quantity <= $product->stock_minimum && $product->stock_minimum > 0)
                            <span class="status-chip status-inactive">Baixo</span>
                        @endif
                    </strong>
                </div>

                <div>
                    <span>Estoque mínimo</span>
                    <strong>{{ number_format($product->stock_minimum, 0, ',', '.') }}</strong>
                </div>
            @endif

            @if($product->sale_price > 0 && $product->cost_price > 0)
                <div>
                    <span>Margem</span>
                    <strong>{{ number_format((($product->sale_price - $product->cost_price) / $product->cost_price) * 100, 1, ',', '.') }}%</strong>
                </div>
            @endif
        </div>
    </section>
</div>

@if($product->description)
    <section class="module-card customer-notes-card">
        <div class="module-card-header">
            <h2>Descrição</h2>
        </div>
        <div class="customer-notes-content">
            {!! nl2br(e($product->description)) !!}
        </div>
    </section>
@endif
@endsection
