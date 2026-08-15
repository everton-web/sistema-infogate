@extends('layouts.erp')

@section('title', 'Novo fornecedor')

@push('head')
<link rel="stylesheet" href="{{ asset('assets/canalsom-modules.css') }}">
@endpush

@section('content')
<div class="module-heading">
    <div>
        <a href="{{ route('suppliers.index') }}" class="module-back-link">← Voltar para fornecedores</a>
        <h1>Novo fornecedor</h1>
        <p>Cadastre um novo fornecedor.</p>
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

<form method="POST" action="{{ route('suppliers.store') }}" class="module-card module-form">
    @csrf

    @include('suppliers._form', ['supplier' => null])

    <div class="form-footer">
        <a href="{{ route('suppliers.index') }}" class="module-btn module-btn-light">Cancelar</a>
        <button type="submit" class="module-btn module-btn-primary">Salvar fornecedor</button>
    </div>
</form>
@endsection
