@extends('layouts.erp')

@section('title', 'Dashboard')

@section('content')
<div class="page-heading">
    <div>
        <h1>Dashboard</h1>
        <p>
            {{ $currentCompany->trade_name ?? $currentCompany->name ?? 'Canal Som' }}
            <span>· acompanhamento da operação</span>
        </p>
    </div>

    <div class="page-actions">
        <a href="{{ route('customers.create') }}" class="btn btn-secondary">
            + Novo cliente
        </a>

        <a href="{{ route('vehicles.create') }}" class="btn btn-primary">
            + Novo veículo
        </a>
    </div>
</div>

<section class="metric-grid">
    <article class="metric-card">
        <span class="metric-label">Clientes ativos</span>
        <strong class="metric-value success">{{ $activeCustomers }}</strong>
        <small>de {{ $totalCustomers }} cadastrados</small>
    </article>

    <article class="metric-card">
        <span class="metric-label">Veículos</span>
        <strong class="metric-value">{{ $totalVehicles }}</strong>
        <small>Total cadastrado</small>
    </article>

    <article class="metric-card">
        <span class="metric-label">OS em aberto</span>
        <strong class="metric-value muted-value">—</strong>
        <small>Módulo em desenvolvimento</small>
    </article>

    <article class="metric-card">
        <span class="metric-label">Caixa</span>
        <strong class="metric-value muted-value">—</strong>
        <small>Módulo em desenvolvimento</small>
    </article>

    <article class="metric-card">
        <span class="metric-label">A receber</span>
        <strong class="metric-value muted-value">—</strong>
        <small>Módulo em desenvolvimento</small>
    </article>

    <article class="metric-card">
        <span class="metric-label">A pagar</span>
        <strong class="metric-value muted-value">—</strong>
        <small>Módulo em desenvolvimento</small>
    </article>

    <article class="metric-card">
        <span class="metric-label">Estoque baixo</span>
        <strong class="metric-value muted-value">—</strong>
        <small>Módulo em desenvolvimento</small>
    </article>

    <article class="metric-card quick-card">
        <span class="metric-label">Acesso rápido</span>
        <div class="quick-actions">
            <a href="{{ route('customers.create') }}" class="quick-btn">
                Novo cliente
            </a>

            <a href="{{ route('vehicles.create') }}" class="quick-btn">
                Novo veículo
            </a>
        </div>
    </article>
</section>

<section class="dashboard-section">
    <div class="section-header">
        <div>
            <h2>Últimos clientes cadastrados</h2>
            <p>Os 5 clientes mais recentes.</p>
        </div>

        <a href="{{ route('customers.index') }}" class="btn btn-light">
            Ver todos
        </a>
    </div>

    <div class="table-card">
        <div class="table-scroll">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>Telefone</th>
                        <th>Cidade</th>
                        <th>Situação</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentCustomers as $customer)
                        <tr class="clickable-row" onclick="window.location='{{ route('customers.show', $customer) }}'">
                            <td><strong>{{ $customer->name }}</strong></td>
                            <td>{{ $customer->type === 'pj' ? 'PJ' : 'PF' }}</td>
                            <td>{{ $customer->phone ?: '—' }}</td>
                            <td>{{ $customer->city ? $customer->city . '/' . $customer->state : '—' }}</td>
                            <td>
                                @if($customer->status === 'inactive')
                                    <span class="status-pill neutral-pill">Inativo</span>
                                @else
                                    <span class="status-pill success-pill">Ativo</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr class="empty-row">
                            <td colspan="5">Nenhum cliente cadastrado ainda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

<section class="dashboard-section">
    <div class="section-header">
        <div>
            <h2>Últimos veículos cadastrados</h2>
            <p>Os 5 veículos mais recentes.</p>
        </div>

        <a href="{{ route('vehicles.index') }}" class="btn btn-light">
            Ver todos
        </a>
    </div>

    <div class="table-card">
        <div class="table-scroll">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th>Placa</th>
                        <th>Cliente</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Ano</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentVehicles as $vehicle)
                        <tr class="clickable-row" onclick="window.location='{{ route('vehicles.show', $vehicle) }}'">
                            <td><span class="plate-badge">{{ $vehicle->plate }}</span></td>
                            <td><strong>{{ $vehicle->customer?->name ?? '—' }}</strong></td>
                            <td>{{ $vehicle->brand?->name ?? '—' }}</td>
                            <td>{{ $vehicle->model?->name ?? '—' }}</td>
                            <td>{{ $vehicle->year_manufacture ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr class="empty-row">
                            <td colspan="5">Nenhum veículo cadastrado ainda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
