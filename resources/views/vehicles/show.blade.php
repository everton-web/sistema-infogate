@extends('layouts.erp')

@section('title', $vehicle->plate)

@push('head')
<link rel="stylesheet" href="{{ asset('assets/canalsom-modules.css') }}">
@endpush

@section('content')
<div class="module-heading">
    <div>
        <a href="{{ route('vehicles.index') }}" class="module-back-link">← Voltar para veículos</a>
        <h1><span class="plate-badge plate-badge-lg">{{ $vehicle->plate }}</span></h1>
        <p>{{ $vehicle->brand?->name }} {{ $vehicle->model?->name }}</p>
    </div>

    <div class="module-actions">
        <a href="{{ route('vehicles.edit', $vehicle) }}" class="module-btn module-btn-primary">
            Editar veículo
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
            <h2>Dados do veículo</h2>
        </div>

        <div class="customer-details">
            <div>
                <span>Marca</span>
                <strong>{{ $vehicle->brand?->name ?? '—' }}</strong>
            </div>

            <div>
                <span>Modelo</span>
                <strong>{{ $vehicle->model?->name ?? '—' }}</strong>
            </div>

            <div>
                <span>Versão</span>
                <strong>{{ $vehicle->version ?: '—' }}</strong>
            </div>

            <div>
                <span>Ano fabricação</span>
                <strong>{{ $vehicle->year_manufacture ?: '—' }}</strong>
            </div>

            <div>
                <span>Ano modelo</span>
                <strong>{{ $vehicle->year_model ?: '—' }}</strong>
            </div>

            <div>
                <span>Cor</span>
                <strong>{{ $vehicle->color ?: '—' }}</strong>
            </div>

            <div>
                <span>Chassi</span>
                <strong>{{ $vehicle->chassis ?: '—' }}</strong>
            </div>

            <div>
                <span>Quilometragem</span>
                <strong>
                    @if(!is_null($vehicle->odometer))
                        {{ number_format($vehicle->odometer, 0, ',', '.') }} km
                    @else
                        —
                    @endif
                </strong>
            </div>

            <div>
                <span>Situação</span>
                <strong>
                    @if($vehicle->status === 'inactive')
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
            <h2>Cliente</h2>
        </div>

        @if($vehicle->customer)
            <div class="customer-details">
                <div>
                    <span>Nome</span>
                    <strong>
                        <a href="{{ route('customers.show', $vehicle->customer) }}">
                            {{ $vehicle->customer->name }}
                        </a>
                    </strong>
                </div>

                <div>
                    <span>Telefone</span>
                    <strong>{{ $vehicle->customer->phone ?: '—' }}</strong>
                </div>

                <div>
                    <span>WhatsApp</span>
                    <strong>{{ $vehicle->customer->whatsapp ?: '—' }}</strong>
                </div>

                <div>
                    <span>E-mail</span>
                    <strong>{{ $vehicle->customer->email ?: '—' }}</strong>
                </div>
            </div>
        @else
            <div class="module-empty compact-empty">
                <strong>Sem cliente vinculado.</strong>
            </div>
        @endif
    </section>
</div>

@if($vehicle->notes)
    <section class="module-card customer-notes-card">
        <div class="module-card-header">
            <h2>Observações</h2>
        </div>
        <div class="customer-notes-content">
            {!! nl2br(e($vehicle->notes)) !!}
        </div>
    </section>
@endif
@endsection
