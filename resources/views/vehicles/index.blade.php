\
@extends('layouts.erp')

@section('title', 'Veículos')

@push('head')
<link rel="stylesheet" href="{{ asset('assets/canalsom-modules.css') }}">
@endpush

@section('content')
<div class="module-heading">
    <div>
        <h1>Veículos</h1>
        <p>Veículos cadastrados para {{ $currentCompany->trade_name ?? $currentCompany->name }}.</p>
    </div>

    <div class="module-actions">
        <a href="{{ route('vehicles.create') }}" class="module-btn module-btn-primary">
            + Novo veículo
        </a>
    </div>
</div>

@if(session('success'))
    <div class="module-alert module-alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="module-summary-grid">
    <div class="summary-card">
        <span>Total de veículos</span>
        <strong>{{ $vehicles->total() }}</strong>
    </div>

    <div class="summary-card">
        <span>Filial ativa</span>
        <strong>{{ $currentBranch->name ?? 'Matriz' }}</strong>
    </div>
</div>

<div class="module-card">
    <div class="module-card-header">
        <div>
            <h2>Lista de veículos</h2>
            <p>Consulte os veículos vinculados aos clientes da Canal Som.</p>
        </div>
    </div>

    @if($vehicles->count())
        <div class="module-table-scroll">
            <table class="module-table">
                <thead>
                    <tr>
                        <th>Placa</th>
                        <th>Cliente</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Ano</th>
                        <th>Cor</th>
                        <th>Quilometragem</th>
                        <th class="cell-right">Situação</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($vehicles as $vehicle)
                        <tr class="clickable-row" onclick="window.location='{{ route('vehicles.show', $vehicle) }}'">
                            <td>
                                <span class="plate-badge">{{ $vehicle->plate }}</span>
                            </td>

                            <td>
                                <strong>{{ $vehicle->customer?->name ?? '—' }}</strong>
                            </td>

                            <td>{{ $vehicle->brand?->name ?? '—' }}</td>

                            <td>
                                <strong>{{ $vehicle->model?->name ?? '—' }}</strong>
                            </td>

                            <td>{{ $vehicle->year_manufacture ?? '—' }}</td>

                            <td>{{ $vehicle->color ?? '—' }}</td>

                            <td>
                                @if(!is_null($vehicle->odometer))
                                    {{ number_format($vehicle->odometer, 0, ',', '.') }} km
                                @else
                                    —
                                @endif
                            </td>

                            <td class="cell-right">
                                @if($vehicle->status === 'inactive')
                                    <span class="status-chip status-inactive">Inativo</span>
                                @else
                                    <span class="status-chip status-active">Ativo</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($vehicles->hasPages())
            <div class="simple-pagination">
                @if($vehicles->onFirstPage())
                    <span class="pagination-disabled">← Anterior</span>
                @else
                    <a href="{{ $vehicles->previousPageUrl() }}">← Anterior</a>
                @endif

                <span>
                    Página {{ $vehicles->currentPage() }} de {{ $vehicles->lastPage() }}
                </span>

                @if($vehicles->hasMorePages())
                    <a href="{{ $vehicles->nextPageUrl() }}">Próxima →</a>
                @else
                    <span class="pagination-disabled">Próxima →</span>
                @endif
            </div>
        @endif
    @else
        <div class="module-empty">
            <strong>Nenhum veículo cadastrado.</strong>
            <p>Cadastre o primeiro veículo para começar a formar o histórico dos clientes.</p>

            <a href="{{ route('vehicles.create') }}" class="module-btn module-btn-primary">
                Cadastrar veículo
            </a>
        </div>
    @endif
</div>
@endsection
