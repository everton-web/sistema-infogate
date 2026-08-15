@extends('layouts.erp')

@section('title', 'Ordens de Serviço')

@push('head')
<link rel="stylesheet" href="{{ asset('assets/canalsom-modules.css') }}">
@endpush

@section('content')
<div class="module-heading">
    <div>
        <h1>Ordens de Serviço</h1>
        <p>Gerenciamento de OS da {{ $currentCompany->trade_name ?? $currentCompany->name }}.</p>
    </div>

    <div class="module-actions">
        <a href="{{ route('service-orders.create') }}" class="module-btn module-btn-primary">
            + Nova OS
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
            <h2>Lista de OS</h2>
        </div>

        <form method="GET" action="{{ route('service-orders.index') }}" class="module-search-form">
            <input
                type="text"
                name="q"
                value="{{ $search ?? '' }}"
                placeholder="Buscar por número ou cliente..."
                class="module-search-input"
            >

            <select name="status" class="module-filter-select" onchange="this.form.submit()">
                <option value="all">Todos os status</option>
                <option value="open" @selected(($status ?? '') === 'open')>Abertas</option>
                <option value="in_progress" @selected(($status ?? '') === 'in_progress')>Em andamento</option>
                <option value="waiting_parts" @selected(($status ?? '') === 'waiting_parts')>Aguardando peças</option>
                <option value="waiting_approval" @selected(($status ?? '') === 'waiting_approval')>Aguardando aprovação</option>
                <option value="completed" @selected(($status ?? '') === 'completed')>Concluídas</option>
                <option value="delivered" @selected(($status ?? '') === 'delivered')>Entregues</option>
                <option value="cancelled" @selected(($status ?? '') === 'cancelled')>Canceladas</option>
            </select>

            <button type="submit" class="module-btn module-btn-light">Buscar</button>
        </form>
    </div>

    @if($orders->count())
        <div class="module-table-scroll">
            <table class="module-table">
                <thead>
                    <tr>
                        <th>Nº</th>
                        <th>Cliente</th>
                        <th>Veículo</th>
                        <th>Abertura</th>
                        <th>Total</th>
                        <th class="cell-right">Status</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($orders as $order)
                        <tr class="clickable-row" onclick="window.location='{{ route('service-orders.show', $order) }}'">
                            <td><strong>#{{ $order->number }}</strong></td>
                            <td>{{ $order->customer?->name ?? '—' }}</td>
                            <td>
                                @if($order->vehicle)
                                    <span class="plate-badge">{{ $order->vehicle->plate }}</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $order->opened_at?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td>R$ {{ number_format($order->total, 2, ',', '.') }}</td>
                            <td class="cell-right">
                                <span class="status-pill {{ $order->statusColor() }}">
                                    {{ $order->statusLabel() }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="simple-pagination">
                @if($orders->onFirstPage())
                    <span class="pagination-disabled">← Anterior</span>
                @else
                    <a href="{{ $orders->previousPageUrl() }}">← Anterior</a>
                @endif

                <span>Página {{ $orders->currentPage() }} de {{ $orders->lastPage() }}</span>

                @if($orders->hasMorePages())
                    <a href="{{ $orders->nextPageUrl() }}">Próxima →</a>
                @else
                    <span class="pagination-disabled">Próxima →</span>
                @endif
            </div>
        @endif
    @else
        <div class="module-empty">
            <strong>Nenhuma ordem de serviço encontrada.</strong>
            <p>Crie a primeira OS para começar a gerenciar os serviços.</p>

            <a href="{{ route('service-orders.create') }}" class="module-btn module-btn-primary">
                Criar OS
            </a>
        </div>
    @endif
</div>
@endsection
