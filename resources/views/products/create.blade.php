@extends('layouts.erp')

@section('title', 'Novo produto/serviço')

@push('head')
<link rel="stylesheet" href="{{ asset('assets/canalsom-modules.css') }}">
@endpush

@section('content')
<div class="module-heading">
    <div>
        <a href="{{ route('products.index') }}" class="module-back-link">← Voltar para produtos</a>
        <h1>Novo produto/serviço</h1>
        <p>Cadastre um novo item no catálogo.</p>
    </div>
</div>

@if ($errors->any())
    <div class="module-alert module-alert-error">
        <strong>Confira os campos informados:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('products.store') }}" class="module-card module-form">
    @csrf

    @include('products._form', ['product' => null])

    <div class="form-footer">
        <a href="{{ route('products.index') }}" class="module-btn module-btn-light">Cancelar</a>
        <button type="submit" class="module-btn module-btn-primary">Salvar item</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const typeSelect = document.getElementById('type');
    const stockQty = document.getElementById('stock-quantity-field');
    const stockMin = document.getElementById('stock-minimum-field');

    function toggleStockFields() {
        const isService = typeSelect.value === 'service';
        stockQty.style.display = isService ? 'none' : '';
        stockMin.style.display = isService ? 'none' : '';
    }

    typeSelect.addEventListener('change', toggleStockFields);
    toggleStockFields();
});
</script>
@endpush
