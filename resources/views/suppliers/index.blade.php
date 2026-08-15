@extends('layouts.erp')

@section('title', 'Fornecedores')

@push('head')
<link rel="stylesheet" href="{{ asset('assets/canalsom-modules.css') }}">
@endpush

@section('content')
<div class="module-heading">
    <div>
        <h1>Fornecedores</h1>
        <p>Fornecedores cadastrados para {{ $currentCompany->trade_name ?? $currentCompany->name }}.</p>
    </div>

    <div class="module-actions">
        <a href="{{ route('suppliers.create') }}" class="module-btn module-btn-primary">
            + Novo fornecedor
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
            <h2>Lista de fornecedores</h2>
        </div>

        <form method="GET" action="{{ route('suppliers.index') }}" class="module-search-form">
            <input
                type="text"
                name="q"
                value="{{ $search ?? '' }}"
                placeholder="Buscar por nome, CNPJ, contato..."
                class="module-search-input"
            >

            <select name="status" class="module-filter-select" onchange="this.form.submit()">
                <option value="">Todas as situações</option>
                <option value="active" @selected(($status ?? '') === 'active')>Ativos</option>
                <option value="inactive" @selected(($status ?? '') === 'inactive')>Inativos</option>
            </select>

            <button type="submit" class="module-btn module-btn-light">Buscar</button>
        </form>
    </div>

    @if($suppliers->count())
        <div class="module-table-scroll">
            <table class="module-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Contato</th>
                        <th>Telefone</th>
                        <th>Cidade</th>
                        <th class="cell-right">Situação</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($suppliers as $supplier)
                        <tr class="clickable-row" onclick="window.location='{{ route('suppliers.show', $supplier) }}'">
                            <td>
                                <strong>{{ $supplier->name }}</strong>
                                @if($supplier->trade_name)
                                    <br><small>{{ $supplier->trade_name }}</small>
                                @endif
                            </td>
                            <td>{{ $supplier->contact_person ?: '—' }}</td>
                            <td>{{ $supplier->phone ?: '—' }}</td>
                            <td>{{ $supplier->city ? $supplier->city . '/' . $supplier->state : '—' }}</td>
                            <td class="cell-right">
                                @if($supplier->status === 'inactive')
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

        @if($suppliers->hasPages())
            <div class="simple-pagination">
                @if($suppliers->onFirstPage())
                    <span class="pagination-disabled">← Anterior</span>
                @else
                    <a href="{{ $suppliers->previousPageUrl() }}">← Anterior</a>
                @endif

                <span>Página {{ $suppliers->currentPage() }} de {{ $suppliers->lastPage() }}</span>

                @if($suppliers->hasMorePages())
                    <a href="{{ $suppliers->nextPageUrl() }}">Próxima →</a>
                @else
                    <span class="pagination-disabled">Próxima →</span>
                @endif
            </div>
        @endif
    @else
        <div class="module-empty">
            <strong>Nenhum fornecedor cadastrado.</strong>
            <p>Cadastre o primeiro fornecedor para gerenciar suas compras.</p>

            <a href="{{ route('suppliers.create') }}" class="module-btn module-btn-primary">
                Cadastrar fornecedor
            </a>
        </div>
    @endif
</div>
@endsection
