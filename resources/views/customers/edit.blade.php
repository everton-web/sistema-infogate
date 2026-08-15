@extends('layouts.erp')

@section('title', 'Editar cliente')

@push('head')
<link rel="stylesheet" href="{{ asset('assets/canalsom-modules.css') }}">
<link rel="stylesheet" href="{{ asset('assets/canalsom-customers.css') }}">
@endpush

@section('content')
<div class="module-heading">
    <div>
        <a href="{{ route('customers.show', $customer) }}" class="module-back-link">← Voltar para o cliente</a>
        <h1>Editar cliente</h1>
        <p>{{ $customer->name }}</p>
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

<form method="POST" action="{{ route('customers.update', $customer) }}" class="module-card customer-form">
    @csrf
    @method('PUT')

    @include('customers._form', ['customer' => $customer])

    <div class="form-footer">
        <a href="{{ route('customers.show', $customer) }}" class="module-btn module-btn-light">Cancelar</a>
        <button type="submit" class="module-btn module-btn-primary">Salvar alterações</button>
    </div>
</form>
@endsection
