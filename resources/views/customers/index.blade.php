@extends('layouts.erp')

@section('title', 'Clientes')

@push('head')
<link rel="stylesheet" href="{{ asset('assets/canalsom-modules.css') }}">
<link rel="stylesheet" href="{{ asset('assets/canalsom-customers.css') }}">
@endpush

@section('content')
<div class="module-heading">
    <div>
        <h1>Clientes</h1>
        <p>Cadastros de pessoas físicas e jurídicas.</p>
    </div>

    <div class="module-actions">
        <a href="{{ route('customers.create') }}" class="module-btn module-btn-primary">
            + Novo cliente
        </a>
    </div>
</div>

@if(session('success'))
    <div class="module-alert module-alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="module-card customer-search-card">
    <form method="GET" action="{{ route('customers.index') }}" class="customer-search-form">
        <div class="customer-search-main">
            <label for="q">Buscar cliente</label>
            <input
                type="search"
                id="q"
                name="q"
                value="{{ $search }}"
                placeholder="Nome, CPF/CNPJ, telefone ou e-mail"
            >
        </div>

        <div class="customer-search-status">
            <label for="status">Situação</label>
            <select id="status" name="status">
                <option value="">Todos</option>
                <option value="active" @selected($status === 'active')>Ativos</option>
                <option value="inactive" @selected($status === 'inactive')>Inativos</option>
            </select>
        </div>

        <button type="submit" class="module-btn module-btn-dark">Buscar</button>

        @if($search || $status)
            <a href="{{ route('customers.index') }}" class="customer-clear-filter">Limpar</a>
        @endif
    </form>
</div>

<div class="module-summary-grid customer-summary">
    <div class="summary-card">
        <span>Clientes encontrados</span>
        <strong>{{ $customers->total() }}</strong>
    </div>
</div>

<div class="module-card">
    <div class="module-card-header">
        <div>
            <h2>Lista de clientes</h2>
            <p>Clique no cliente para consultar dados e veículos vinculados.</p>
        </div>
    </div>

    @if($customers->count())
        <div class="module-table-scroll">
            <table class="module-table customer-table">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>CPF / CNPJ</th>
                        <th>Telefone</th>
                        <th>Veículos</th>
                        <th>Tipo</th>
                        <th class="cell-right">Situação</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $customer)
                        <tr class="clickable-row" onclick="window.location='{{ route('customers.show', $customer) }}'">
                            <td>
                                <div class="customer-name-cell">
                                    <strong>{{ $customer->name }}</strong>
                                    @if($customer->email)
                                        <small>{{ $customer->email }}</small>
                                    @endif
                                </div>
                            </td>

                            <td>
                                @if($customer->document)
                                    <span class="document-value"
                                          data-document="{{ $customer->document }}"
                                          data-type="{{ $customer->type }}">
                                        {{ $customer->document }}
                                    </span>
                                @else
                                    —
                                @endif
                            </td>

                            <td>{{ $customer->whatsapp ?: ($customer->phone ?: '—') }}</td>
                            <td><strong>{{ $customer->vehicles_count }}</strong></td>
                            <td>{{ $customer->type === 'pj' ? 'PJ' : 'PF' }}</td>
                            <td class="cell-right">
                                @if($customer->status === 'inactive')
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

        @if($customers->hasPages())
            <div class="simple-pagination">
                @if($customers->onFirstPage())
                    <span class="pagination-disabled">← Anterior</span>
                @else
                    <a href="{{ $customers->previousPageUrl() }}">← Anterior</a>
                @endif

                <span>Página {{ $customers->currentPage() }} de {{ $customers->lastPage() }}</span>

                @if($customers->hasMorePages())
                    <a href="{{ $customers->nextPageUrl() }}">Próxima →</a>
                @else
                    <span class="pagination-disabled">Próxima →</span>
                @endif
            </div>
        @endif
    @else
        <div class="module-empty">
            <strong>Nenhum cliente encontrado.</strong>
            <p>Cadastre o primeiro cliente ou ajuste os filtros da pesquisa.</p>
            <a href="{{ route('customers.create') }}" class="module-btn module-btn-primary">
                Cadastrar cliente
            </a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.document-value').forEach((el) => {
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
});
</script>
@endpush
