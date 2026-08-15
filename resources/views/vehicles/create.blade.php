\
@extends('layouts.erp')

@section('title', 'Novo veículo')

@push('head')
<link rel="stylesheet" href="{{ asset('assets/canalsom-modules.css') }}">
@endpush

@section('content')
<div class="module-heading">
    <div>
        <a href="{{ route('vehicles.index') }}" class="module-back-link">← Voltar para veículos</a>
        <h1>Novo veículo</h1>
        <p>Cadastre o cliente e os dados principais do veículo.</p>
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

<form method="POST" action="{{ route('vehicles.store') }}" class="module-card module-form">
    @csrf

    <div class="form-section">
        <div class="form-section-heading">
            <div>
                <span class="form-step">1</span>
                <div>
                    <h2>Cliente</h2>
                    <p>Selecione um cliente existente ou faça um cadastro rápido.</p>
                </div>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-field form-field-full">
                <label for="customer_id">Cliente existente</label>
                <select name="customer_id" id="customer_id">
                    <option value="">Cadastrar novo cliente</option>

                    @foreach($customers as $customer)
                        <option
                            value="{{ $customer->id }}"
                            @selected(old('customer_id') == $customer->id)
                        >
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="new-customer-fields" class="form-grid nested-grid form-field-full">
                <div class="form-field">
                    <label for="customer_name">Nome do cliente</label>
                    <input
                        type="text"
                        id="customer_name"
                        name="customer_name"
                        value="{{ old('customer_name') }}"
                        autocomplete="name"
                    >
                </div>

                <div class="form-field">
                    <label for="customer_phone">Telefone / WhatsApp</label>
                    <input
                        type="text"
                        id="customer_phone"
                        name="customer_phone"
                        value="{{ old('customer_phone') }}"
                        placeholder="(71) 99999-9999"
                        maxlength="14"
                    >
                </div>
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
                            @selected(old('vehicle_brand_id') == $brand->id)
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
                    data-old="{{ old('vehicle_model_id') }}"
                    required
                    disabled
                >
                    <option value="">Selecione primeiro a marca</option>
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
                    value="{{ old('plate') }}"
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
                    value="{{ old('year_manufacture') }}"
                    placeholder="2000"
                >
            </div>
        </div>

        <div class="form-grid vehicle-secondary-grid">
            <div class="form-field">
                <label for="color">Cor</label>
                <input
                    type="text"
                    id="color"
                    name="color"
                    value="{{ old('color') }}"
                    placeholder="Ex.: Prata"
                >
            </div>

            <div class="form-field">
                <label for="odometer">Quilometragem</label>
                <input
                    type="number"
                    id="odometer"
                    name="odometer"
                    min="0"
                    value="{{ old('odometer') }}"
                    placeholder="Ex.: 260000"
                >
            </div>

            <div class="form-field form-field-full">
                <label for="notes">Observações</label>
                <textarea
                    id="notes"
                    name="notes"
                    rows="4"
                    placeholder="Informações adicionais sobre o veículo..."
                >{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>

    <div class="form-footer">
        <a href="{{ route('vehicles.index') }}" class="module-btn module-btn-light">
            Cancelar
        </a>

        <button type="submit" class="module-btn module-btn-primary">
            Salvar veículo
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const customerSelect = document.getElementById('customer_id');
    const newCustomerFields = document.getElementById('new-customer-fields');
    const brandSelect = document.getElementById('vehicle_brand_id');
    const modelSelect = document.getElementById('vehicle_model_id');
    const plateInput = document.getElementById('plate');
    const yearInput = document.getElementById('year_manufacture');
    const phoneInput = document.getElementById('customer_phone');
phoneInput.addEventListener('input', function () {
    let value = this.value.replace(/\D/g, '').slice(0, 11);

    if (value.length > 2) {
        value = '(' + value.slice(0, 2) + ')' + value.slice(2);
    }

    if (value.length > 9) {
        value = value.slice(0, 9) + '-' + value.slice(9);
    }

    this.value = value;
});

    function toggleCustomerFields() {
        newCustomerFields.style.display = customerSelect.value ? 'none' : 'grid';
    }

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

    customerSelect.addEventListener('change', toggleCustomerFields);
    brandSelect.addEventListener('change', loadModels);

    plateInput.addEventListener('input', function () {
        this.value = this.value
            .toUpperCase()
            .replace(/[^A-Z0-9-]/g, '');
    });

    yearInput.addEventListener('input', function () {
        this.value = this.value
            .replace(/\D/g, '')
            .slice(0, 4);
    });

    toggleCustomerFields();

    if (brandSelect.value) {
        loadModels();
    }
});
</script>
@endpush
