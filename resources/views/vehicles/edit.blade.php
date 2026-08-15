@extends('layouts.erp')

@section('title', 'Editar veículo')

@push('head')
<link rel="stylesheet" href="{{ asset('assets/canalsom-modules.css') }}">
@endpush

@section('content')
<div class="module-heading">
    <div>
        <a href="{{ route('vehicles.show', $vehicle) }}" class="module-back-link">← Voltar para o veículo</a>
        <h1>Editar veículo</h1>
        <p><span class="plate-badge">{{ $vehicle->plate }}</span> {{ $vehicle->brand?->name }} {{ $vehicle->model?->name }}</p>
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

<form method="POST" action="{{ route('vehicles.update', $vehicle) }}" class="module-card module-form">
    @csrf
    @method('PUT')

    <div class="form-section">
        <div class="form-section-heading">
            <div>
                <span class="form-step">1</span>
                <div>
                    <h2>Cliente</h2>
                    <p>Selecione o cliente vinculado a este veículo.</p>
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
                            @selected(old('customer_id', $vehicle->customer_id) == $customer->id)
                        >
                            {{ $customer->name }}
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
                    <h2>Veículo</h2>
                    <p>Marca e modelo usam o catálogo automotivo importado.</p>
                </div>
            </div>
        </div>

        <div class="form-grid vehicle-main-grid">
            <div class="form-field">
                <label for="vehicle_brand_id">Marca *</label>
                <select name="vehicle_brand_id" id="vehicle_brand_id" required>
                    <option value="">Selecione a marca</option>

                    @foreach($brands as $brand)
                        <option
                            value="{{ $brand->id }}"
                            @selected(old('vehicle_brand_id', $vehicle->vehicle_brand_id) == $brand->id)
                        >
                            {{ $brand->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-field">
                <label for="vehicle_model_id">Modelo *</label>
                <select
                    name="vehicle_model_id"
                    id="vehicle_model_id"
                    data-old="{{ old('vehicle_model_id', $vehicle->vehicle_model_id) }}"
                    required
                    disabled
                >
                    <option value="">Carregando modelos...</option>
                </select>
            </div>
        </div>

        <div class="vehicle-compact-row">
            <div class="form-field vehicle-plate-field">
                <label for="plate">Placa *</label>
                <input
                    type="text"
                    id="plate"
                    name="plate"
                    maxlength="8"
                    value="{{ old('plate', $vehicle->plate) }}"
                    placeholder="ABC-1234 ou ABC1D23"
                    required
                >
                <small>Placa antiga ou Mercosul. O padrão digitado é preservado.</small>
            </div>

            <div class="form-field vehicle-year-field">
                <label for="year_manufacture">Ano fabricação</label>
                <input
                    type="text"
                    inputmode="numeric"
                    pattern="[0-9]{4}"
                    maxlength="4"
                    id="year_manufacture"
                    name="year_manufacture"
                    value="{{ old('year_manufacture', $vehicle->year_manufacture) }}"
                    placeholder="2000"
                >
            </div>
        </div>

        <div class="form-grid vehicle-secondary-grid">
            <div class="form-field">
                <label for="year_model">Ano modelo</label>
                <input
                    type="text"
                    inputmode="numeric"
                    pattern="[0-9]{4}"
                    maxlength="4"
                    id="year_model"
                    name="year_model"
                    value="{{ old('year_model', $vehicle->year_model) }}"
                    placeholder="2001"
                >
            </div>

            <div class="form-field">
                <label for="version">Versão</label>
                <input
                    type="text"
                    id="version"
                    name="version"
                    value="{{ old('version', $vehicle->version) }}"
                    placeholder="Ex.: 1.0 Flex"
                >
            </div>

            <div class="form-field">
                <label for="color">Cor</label>
                <input
                    type="text"
                    id="color"
                    name="color"
                    value="{{ old('color', $vehicle->color) }}"
                    placeholder="Ex.: Prata"
                >
            </div>

            <div class="form-field">
                <label for="chassis">Chassi</label>
                <input
                    type="text"
                    id="chassis"
                    name="chassis"
                    maxlength="30"
                    value="{{ old('chassis', $vehicle->chassis) }}"
                >
            </div>

            <div class="form-field">
                <label for="odometer">Quilometragem</label>
                <input
                    type="number"
                    id="odometer"
                    name="odometer"
                    min="0"
                    value="{{ old('odometer', $vehicle->odometer) }}"
                    placeholder="Ex.: 260000"
                >
            </div>

            <div class="form-field">
                <label for="status">Situação *</label>
                <select name="status" id="status" required>
                    <option value="active" @selected(old('status', $vehicle->status) === 'active')>Ativo</option>
                    <option value="inactive" @selected(old('status', $vehicle->status) === 'inactive')>Inativo</option>
                </select>
            </div>

            <div class="form-field form-field-full">
                <label for="notes">Observações</label>
                <textarea
                    id="notes"
                    name="notes"
                    rows="4"
                    placeholder="Informações adicionais sobre o veículo..."
                >{{ old('notes', $vehicle->notes) }}</textarea>
            </div>
        </div>
    </div>

    <div class="form-footer">
        <a href="{{ route('vehicles.show', $vehicle) }}" class="module-btn module-btn-light">
            Cancelar
        </a>

        <button type="submit" class="module-btn module-btn-primary">
            Salvar alterações
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const brandSelect = document.getElementById('vehicle_brand_id');
    const modelSelect = document.getElementById('vehicle_model_id');
    const plateInput = document.getElementById('plate');
    const yearInputs = document.querySelectorAll('#year_manufacture, #year_model');

    async function loadModels() {
        const brandId = brandSelect.value;
        const oldModel = modelSelect.dataset.old;

        modelSelect.disabled = true;

        if (!brandId) {
            modelSelect.innerHTML = '<option value="">Selecione primeiro a marca</option>';
            return;
        }

        modelSelect.innerHTML = '<option value="">Carregando modelos...</option>';

        try {
            const response = await fetch(`/cadastros/veiculos/modelos/${brandId}`, {
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const models = await response.json();

            modelSelect.innerHTML = '<option value="">Selecione o modelo</option>';

            models.forEach((model) => {
                const option = document.createElement('option');
                option.value = model.id;
                option.textContent = model.name;

                if (oldModel && String(model.id) === String(oldModel)) {
                    option.selected = true;
                }

                modelSelect.appendChild(option);
            });

            modelSelect.disabled = false;
        } catch (error) {
            console.error(error);
            modelSelect.innerHTML = '<option value="">Erro ao carregar modelos</option>';
        }
    }

    brandSelect.addEventListener('change', loadModels);

    plateInput.addEventListener('input', function () {
        this.value = this.value
            .toUpperCase()
            .replace(/[^A-Z0-9-]/g, '');
    });

    yearInputs.forEach(input => {
        input.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 4);
        });
    });

    if (brandSelect.value) {
        loadModels();
    }
});
</script>
@endpush
