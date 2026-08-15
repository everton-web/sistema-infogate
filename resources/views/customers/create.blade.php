@extends('layouts.erp')

@section('title', 'Novo cliente')

@push('head')
<link rel="stylesheet" href="{{ asset('assets/canalsom-modules.css') }}">
<link rel="stylesheet" href="{{ asset('assets/canalsom-customers.css') }}">
@endpush

@section('content')
<div class="module-heading">
    <div>
        <a href="{{ route('customers.index') }}" class="module-back-link">← Voltar para clientes</a>
        <h1>Novo cliente</h1>
        <p>Cadastre uma pessoa física ou jurídica.</p>
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

<form method="POST" action="{{ route('customers.store') }}" class="module-card customer-form">
    @csrf

    @include('customers._form')

    <div class="form-footer">
        <a href="{{ route('customers.index') }}" class="module-btn module-btn-light">Cancelar</a>
        <button type="submit" class="module-btn module-btn-primary">Salvar cliente</button>
    </div>
</form>
@endsection
