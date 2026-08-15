@extends('layouts.erp')

@section('title', $supplier->name)

@push('head')
<link rel="stylesheet" href="{{ asset('assets/canalsom-modules.css') }}">
@endpush

@section('content')
<div class="module-heading">
    <div>
        <a href="{{ route('suppliers.index') }}" class="module-back-link">← Voltar para fornecedores</a>
        <h1>{{ $supplier->name }}</h1>
        <p>
            {{ $supplier->type === 'pj' ? 'Pessoa Jurídica' : 'Pessoa Física' }}
            @if($supplier->trade_name)
                · {{ $supplier->trade_name }}
            @endif
        </p>
    </div>

    <div class="module-actions">
        <a href="{{ route('suppliers.edit', $supplier) }}" class="module-btn module-btn-primary">
            Editar fornecedor
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
            <h2>Dados do fornecedor</h2>
        </div>

        <div class="customer-details">
            <div>
                <span>Documento</span>
                <strong id="supplierDocument"
                        data-document="{{ $supplier->document }}"
                        data-type="{{ $supplier->type }}">
                    {{ $supplier->document ?: '—' }}
                </strong>
            </div>

            <div>
                <span>Pessoa de contato</span>
                <strong>{{ $supplier->contact_person ?: '—' }}</strong>
            </div>

            <div>
                <span>Telefone</span>
                <strong>{{ $supplier->phone ?: '—' }}</strong>
            </div>

            <div>
                <span>WhatsApp</span>
                <strong>{{ $supplier->whatsapp ?: '—' }}</strong>
            </div>

            <div>
                <span>E-mail</span>
                <strong>{{ $supplier->email ?: '—' }}</strong>
            </div>

            <div>
                <span>Situação</span>
                <strong>
                    @if($supplier->status === 'inactive')
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
            @if($supplier->street || $supplier->city)
                <strong>
                    {{ $supplier->street }}
                    @if($supplier->number), {{ $supplier->number }}@endif
                </strong>

                @if($supplier->complement)
                    <span>{{ $supplier->complement }}</span>
                @endif

                <span>
                    {{ $supplier->neighborhood }}
                    @if($supplier->city) · {{ $supplier->city }} @endif
                    @if($supplier->state) /{{ $supplier->state }} @endif
                </span>

                @if($supplier->postal_code)
                    <span>CEP {{ $supplier->postal_code }}</span>
                @endif
            @else
                <span class="muted-text">Endereço não informado.</span>
            @endif
        </div>
    </section>
</div>

@if($supplier->notes)
    <section class="module-card customer-notes-card">
        <div class="module-card-header">
            <h2>Observações</h2>
        </div>
        <div class="customer-notes-content">
            {!! nl2br(e($supplier->notes)) !!}
        </div>
    </section>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('supplierDocument');
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
