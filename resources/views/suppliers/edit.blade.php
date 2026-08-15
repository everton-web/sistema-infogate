@extends('layouts.erp')

@section('title', 'Editar fornecedor')

@push('head')
<link rel="stylesheet" href="{{ asset('assets/canalsom-modules.css') }}">
@endpush

@section('content')
<div class="module-heading">
    <div>
        <a href="{{ route('suppliers.show', $supplier) }}" class="module-back-link">← Voltar para o fornecedor</a>
        <h1>Editar fornecedor</h1>
        <p>{{ $supplier->name }}</p>
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

<form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="module-card module-form">
    @csrf
    @method('PUT')

    @include('suppliers._form', ['supplier' => $supplier])

    <div class="form-footer">
        <a href="{{ route('suppliers.show', $supplier) }}" class="module-btn module-btn-light">Cancelar</a>
        <button type="submit" class="module-btn module-btn-primary">Salvar alterações</button>
    </div>
</form>
@endsection
