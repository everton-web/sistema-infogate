@extends('layouts.erp')

@section('title', $customer->name)

@push('head')
<link rel="stylesheet" href="{{ asset('assets/canalsom-modules.css') }}">
<link rel="stylesheet" href="{{ asset('assets/canalsom-customers.css') }}">
@endpush

@section('content')
<div class="module-heading">
    <div>
        <a href="{{ route('customers.index') }}" class="module-back-link">← Voltar para clientes</a>
        <h1>{{ $customer->name }}</h1>
        <p>
            {{ $customer->type === 'pj' ? 'Pessoa Jurídica' : 'Pessoa Física' }}
            @if($customer->trade_name)
                · {{ $customer->trade_name }}
            @endif
        </p>
    </div>

    <div class="module-actions">
        <a href="{{ route('vehicles.create', ['customer_id' => $customer->id]) }}" class="module-btn module-btn-light">
            + Novo veículo
        </a>
        <a href="{{ route('customers.edit', $customer) }}" class="module-btn module-btn-primary">
            Editar cliente
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
            <h2>Dados do cliente</h2>
        </div>

        <div class="customer-details">
            <div>
                <span>Documento</span>
                <strong id="customerDocument"
                        data-document="{{ $customer->document }}"
                        data-type="{{ $customer->type }}">
                    {{ $customer->document ?: '—' }}
                </strong>
            </div>

            <div>
                <span>Telefone</span>
                <strong>{{ $customer->phone ?: '—' }}</strong>
            </div>

            <div>
                <span>WhatsApp</span>
                <strong>{{ $customer->whatsapp ?: '—' }}</strong>
            </div>

            <div>
                <span>E-mail</span>
                <strong>{{ $customer->email ?: '—' }}</strong>
            </div>

            <div>
                <span>Situação</span>
                <strong>
                    @if($customer->status === 'inactive')
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
            <h2>Endereço</h2>
        </div>

        <div class="customer-address-block">
            @if($customer->street || $customer->city)
                <strong>
                    {{ $customer->street }}
                    @if($customer->number), {{ $customer->number }}@endif
                </strong>

                @if($customer->complement)
                    <span>{{ $customer->complement }}</span>
                @endif

                <span>
                    {{ $customer->neighborhood }}
                    @if($customer->city)
                        · {{ $customer->city }}
                    @endif
                    @if($customer->state)
                        /{{ $customer->state }}
                    @endif
                </span>

                @if($customer->postal_code)
                    <span>CEP {{ $customer->postal_code }}</span>
                @endif
            @else
                <span class="muted-text">Endereço não informado.</span>
            @endif
        </div>
    </section>
</div>

<section class="module-card customer-vehicles-card">
    <div class="module-card-header customer-card-header-actions">
        <div>
            <h2>Veículos</h2>
            <p>Veículos vinculados a este cliente.</p>
        </div>

        <a href="{{ route('vehicles.create', ['customer_id' => $customer->id]) }}" class="module-btn module-btn-light">
            + Adicionar veículo
        </a>
    </div>

    @if($customer->vehicles->count())
        <div class="module-table-scroll">
            <table class="module-table">
                <thead>
                    <tr>
                        <th>Placa</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Ano</th>
                        <th>Cor</th>
                        <th>Quilometragem</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customer->vehicles as $vehicle)
                        <tr>
                            <td><span class="plate-badge">{{ $vehicle->plate }}</span></td>
                            <td>{{ $vehicle->brand?->name ?? '—' }}</td>
                            <td><strong>{{ $vehicle->model?->name ?? '—' }}</strong></td>
                            <td>{{ $vehicle->year_manufacture ?: '—' }}</td>
                            <td>{{ $vehicle->color ?: '—' }}</td>
                            <td>
                                {{ is_null($vehicle->odometer)
                                    ? '—'
                                    : number_format($vehicle->odometer, 0, ',', '.') . ' km' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="module-empty compact-empty">
            <strong>Nenhum veículo vinculado.</strong>
            <p>Você pode cadastrar o primeiro veículo diretamente para este cliente.</p>
            <a href="{{ route('vehicles.create', ['customer_id' => $customer->id]) }}" class="module-btn module-btn-primary">
                Cadastrar veículo
            </a>
        </div>
    @endif
</section>

@if($customer->notes)
    <section class="module-card customer-notes-card">
        <div class="module-card-header">
            <h2>Observações</h2>
        </div>
        <div class="customer-notes-content">
            {!! nl2br(e($customer->notes)) !!}
        </div>
    </section>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('customerDocument');
    if (!el) return;

    let value = (el.dataset.document || '').replace(/\D/g, '');
    const type = el.dataset.type;

    if (type === 'pj' && value.length === 14) {
        el.textContent =
            value.slice(0,2) + '.' +
            value.slice(2,5) + '.' +
            value.slice(5,8) + '/' +
            value.slice(8,12) + '-' +
            value.slice(12);
    }

    if (type !== 'pj' && value.length === 11) {
        el.textContent =
            value.slice(0,3) + '.' +
            value.slice(3,6) + '.' +
            value.slice(6,9) + '-' +
            value.slice(9);
    }
});
</script>
@endpush
