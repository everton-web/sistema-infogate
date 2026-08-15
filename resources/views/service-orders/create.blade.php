@extends('layouts.erp')

@section('title', 'Nova OS')

@push('head')
<link rel="stylesheet" href="{{ asset('assets/canalsom-modules.css') }}">
@endpush

@section('content')
<div class="module-heading">
    <div>
        <a href="{{ route('service-orders.index') }}" class="module-back-link">← Voltar para OS</a>
        <h1>Nova Ordem de Serviço</h1>
        <p>Preencha os dados para abrir uma nova OS.</p>
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

<form method="POST" action="{{ route('service-orders.store') }}" class="module-card module-form">
    @csrf

    <div class="form-section">
        <div class="form-section-heading">
            <div>
                <span class="form-step">1</span>
                <div>
                    <h2>Cliente e veículo</h2>
                    <p>Selecione o cliente e, opcionalmente, o veículo.</p>
                </div>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-field form-field-full">
                <label for="customer_id">Cliente *</label>
                <select name="customer_id" id="customer_id" required>
                    <option value="">Selecione o cliente</option>
                    @foreach($customers as $customer)
                        <option
                            value="{{ $customer->id }}"
                            @selected(old('customer_id', $selectedCustomer) == $customer->id)
                        >
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-field form-field-full">
                <label for="vehicle_id">Veículo</label>
                <select name="vehicle_id" id="vehicle_id">
                    <option value="">Sem veículo / selecione o cliente primeiro</option>
                    @foreach($vehicles as $vehicle)
                        <option
                            value="{{ $vehicle->id }}"
                            @selected(old('vehicle_id') == $vehicle->id)
                        >
                            {{ $vehicle->plate }} - {{ $vehicle->brand?->name }} {{ $vehicle->model?->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="form-section">
        <div class="form-section-heading">
            <div>
                <span class="form-step">2</span>
                <div>
                    <h2>Detalhes</h2>
                    <p>Reclamação do cliente e prioridade.</p>
                </div>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-field">
                <label for="priority">Prioridade *</label>
                <select name="priority" id="priority" required>
                    <option value="low" @selected(old('priority') === 'low')>Baixa</option>
                    <option value="normal" @selected(old('priority', 'normal') === 'normal')>Normal</option>
                    <option value="high" @selected(old('priority') === 'high')>Alta</option>
                    <option value="urgent" @selected(old('priority') === 'urgent')>Urgente</option>
                </select>
            </div>

            <div class="form-field form-field-full">
                <label for="complaint">Reclamação / solicitação do cliente</label>
                <textarea
                    id="complaint"
                    name="complaint"
                    rows="4"
                    placeholder="Descreva o que o cliente relatou..."
                >{{ old('complaint') }}</textarea>
            </div>

            <div class="form-field form-field-full">
                <label for="internal_notes">Observações internas</label>
                <textarea
                    id="internal_notes"
                    name="internal_notes"
                    rows="3"
                    placeholder="Notas visíveis apenas para a equipe..."
                >{{ old('internal_notes') }}</textarea>
            </div>
        </div>
    </div>

    <div class="form-footer">
        <a href="{{ route('service-orders.index') }}" class="module-btn module-btn-light">Cancelar</a>
        <button type="submit" class="module-btn module-btn-primary">Abrir OS</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const customerSelect = document.getElementById('customer_id');
    const vehicleSelect = document.getElementById('vehicle_id');

    customerSelect.addEventListener('change', async () => {
        const customerId = customerSelect.value;
        vehicleSelect.innerHTML = '<option value="">Carregando veículos...</option>';

        if (!customerId) {
            vehicleSelect.innerHTML = '<option value="">Sem veículo / selecione o cliente primeiro</option>';
            return;
        }

        try {
            const res = await fetch(`/ordens-servico/cliente/${customerId}/veiculos`, {
                headers: { 'Accept': 'application/json' }
            });
            const vehicles = await res.json();

            vehicleSelect.innerHTML = '<option value="">Sem veículo</option>';
            vehicles.forEach(v => {
                const opt = document.createElement('option');
                opt.value = v.id;
                opt.textContent = v.label;
                vehicleSelect.appendChild(opt);
            });
        } catch {
            vehicleSelect.innerHTML = '<option value="">Erro ao carregar veículos</option>';
        }
    });
});
</script>
@endpush
