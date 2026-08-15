@php
    $editing = isset($customer);
    $type = old('type', $customer->type ?? 'pf');
    $statusValue = old('status', $customer->status ?? 'active');

    $documentRaw = old('document', $customer->document ?? '');
    $phoneRaw = old('phone', $customer->phone ?? '');
    $whatsappRaw = old('whatsapp', $customer->whatsapp ?? '');
@endphp

<div class="customer-form-section">
    <div class="form-section-heading">
        <div>
            <span class="form-step">1</span>
            <div>
                <h2>Identificação</h2>
                <p>Dados principais do cliente.</p>
            </div>
        </div>
    </div>

    <div class="customer-type-switch">
        <label class="type-option">
            <input type="radio" name="type" value="pf" @checked($type === 'pf')>
            <span>Pessoa Física</span>
        </label>

        <label class="type-option">
            <input type="radio" name="type" value="pj" @checked($type === 'pj')>
            <span>Pessoa Jurídica</span>
        </label>
    </div>

    <div class="form-grid customer-grid">
        <div class="form-field form-field-wide">
            <label for="name" id="nameLabel">Nome *</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $customer->name ?? '') }}"
                required
                autocomplete="name"
            >
        </div>

        <div class="form-field">
            <label for="document" id="documentLabel">CPF</label>
            <input
                type="text"
                id="document"
                name="document"
                value="{{ $documentRaw }}"
                inputmode="numeric"
                autocomplete="off"
            >
        </div>

        <div class="form-field pj-only">
            <label for="trade_name">Nome fantasia</label>
            <input
                type="text"
                id="trade_name"
                name="trade_name"
                value="{{ old('trade_name', $customer->trade_name ?? '') }}"
            >
        </div>

        <div class="form-field pj-only">
            <label for="state_registration">Inscrição estadual</label>
            <input
                type="text"
                id="state_registration"
                name="state_registration"
                value="{{ old('state_registration', $customer->state_registration ?? '') }}"
            >
        </div>

        <div class="form-field">
            <label for="status">Situação</label>
            <select id="status" name="status" required>
                <option value="active" @selected($statusValue === 'active')>Ativo</option>
                <option value="inactive" @selected($statusValue === 'inactive')>Inativo</option>
            </select>
        </div>
    </div>
</div>

<div class="customer-form-section">
    <div class="form-section-heading">
        <div>
            <span class="form-step">2</span>
            <div>
                <h2>Contato</h2>
                <p>Telefone, WhatsApp e e-mail.</p>
            </div>
        </div>
    </div>

    <div class="form-grid customer-grid">
        <div class="form-field">
            <label for="phone">Telefone</label>
            <input
                type="text"
                id="phone"
                name="phone"
                maxlength="14"
                value="{{ $phoneRaw }}"
                placeholder="(71)99999-9999"
                inputmode="numeric"
            >
        </div>

        <div class="form-field">
            <label for="whatsapp">WhatsApp</label>
            <input
                type="text"
                id="whatsapp"
                name="whatsapp"
                maxlength="14"
                value="{{ $whatsappRaw }}"
                placeholder="(71)99999-9999"
                inputmode="numeric"
            >
        </div>

        <div class="form-field form-field-wide">
            <label for="email">E-mail</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email', $customer->email ?? '') }}"
                autocomplete="email"
            >
        </div>
    </div>
</div>

<div class="customer-form-section">
    <div class="form-section-heading">
        <div>
            <span class="form-step">3</span>
            <div>
                <h2>Endereço</h2>
                <p>Informações opcionais para cadastro e documentos futuros.</p>
            </div>
        </div>
    </div>

    <div class="form-grid customer-address-grid">
        <div class="form-field customer-cep">
            <label for="zip_code">CEP</label>
            <input
                type="text"
                id="postal_code"
                name="postal_code"
                maxlength="9"
                value="{{ old('postal_code', $customer->postal_code ?? '') }}"
                placeholder="00000-000"
                inputmode="numeric"
            >
        </div>

        <div class="form-field customer-street">
            <label for="street">Logradouro</label>
            <input
                type="text"
                id="street"
                name="street"
                value="{{ old('street', $customer->street ?? '') }}"
            >
        </div>

        <div class="form-field customer-number">
            <label for="number">Número</label>
            <input
                type="text"
                id="number"
                name="number"
                value="{{ old('number', $customer->number ?? '') }}"
            >
        </div>

        <div class="form-field">
            <label for="complement">Complemento</label>
            <input
                type="text"
                id="complement"
                name="complement"
                value="{{ old('complement', $customer->complement ?? '') }}"
            >
        </div>

        <div class="form-field">
            <label for="neighborhood">Bairro</label>
            <input
                type="text"
                id="neighborhood"
                name="neighborhood"
                value="{{ old('neighborhood', $customer->neighborhood ?? '') }}"
            >
        </div>

        <div class="form-field">
            <label for="city">Cidade</label>
            <input
                type="text"
                id="city"
                name="city"
                value="{{ old('city', $customer->city ?? '') }}"
            >
        </div>

        <div class="form-field customer-state">
            <label for="state">UF</label>
            <input
                type="text"
                id="state"
                name="state"
                maxlength="2"
                value="{{ old('state', $customer->state ?? 'BA') }}"
                placeholder="BA"
            >
        </div>
    </div>
</div>

<div class="customer-form-section">
    <div class="form-section-heading">
        <div>
            <span class="form-step">4</span>
            <div>
                <h2>Observações</h2>
                <p>Anotações internas sobre o cliente.</p>
            </div>
        </div>
    </div>

    <div class="form-field">
        <textarea
            id="notes"
            name="notes"
            rows="4"
            placeholder="Informações adicionais..."
        >{{ old('notes', $customer->notes ?? '') }}</textarea>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const typeInputs = document.querySelectorAll('input[name="type"]');
    const documentInput = document.getElementById('document');
    const nameLabel = document.getElementById('nameLabel');
    const documentLabel = document.getElementById('documentLabel');
    const phoneInput = document.getElementById('phone');
    const whatsappInput = document.getElementById('whatsapp');
    const zipInput = document.getElementById('postal_code');
    const stateInput = document.getElementById('state');

    function digits(value, max) {
        return value.replace(/\D/g, '').slice(0, max);
    }

    function maskPhone(input) {
        if (!input) return;

        let value = digits(input.value, 11);

        if (value.length > 2) {
            value = '(' + value.slice(0, 2) + ')' + value.slice(2);
        }

        if (value.length > 9) {
            value = value.slice(0, 9) + '-' + value.slice(9);
        }

        input.value = value;
    }

    function maskCpf(value) {
        value = digits(value, 11);

        if (value.length > 3) value = value.slice(0, 3) + '.' + value.slice(3);
        if (value.length > 7) value = value.slice(0, 7) + '.' + value.slice(7);
        if (value.length > 11) value = value.slice(0, 11) + '-' + value.slice(11);

        return value;
    }

    function maskCnpj(value) {
        value = digits(value, 14);

        if (value.length > 2) value = value.slice(0, 2) + '.' + value.slice(2);
        if (value.length > 6) value = value.slice(0, 6) + '.' + value.slice(6);
        if (value.length > 10) value = value.slice(0, 10) + '/' + value.slice(10);
        if (value.length > 15) value = value.slice(0, 15) + '-' + value.slice(15);

        return value;
    }

    function currentType() {
        return document.querySelector('input[name="type"]:checked')?.value || 'pf';
    }

    function updateTypeUI() {
        const type = currentType();
        const isPJ = type === 'pj';

        document.querySelectorAll('.pj-only').forEach((el) => {
            el.style.display = isPJ ? '' : 'none';
        });

        nameLabel.textContent = isPJ ? 'Razão social *' : 'Nome *';
        documentLabel.textContent = isPJ ? 'CNPJ' : 'CPF';
        documentInput.placeholder = isPJ ? '00.000.000/0000-00' : '000.000.000-00';
        documentInput.maxLength = isPJ ? 18 : 14;
        documentInput.value = isPJ
            ? maskCnpj(documentInput.value)
            : maskCpf(documentInput.value);
    }

    documentInput?.addEventListener('input', () => {
        documentInput.value = currentType() === 'pj'
            ? maskCnpj(documentInput.value)
            : maskCpf(documentInput.value);
    });

    phoneInput?.addEventListener('input', () => maskPhone(phoneInput));
    whatsappInput?.addEventListener('input', () => maskPhone(whatsappInput));

    zipInput?.addEventListener('input', () => {
        let value = digits(zipInput.value, 8);
        if (value.length > 5) {
            value = value.slice(0, 5) + '-' + value.slice(5);
        }
        zipInput.value = value;
    });

    stateInput?.addEventListener('input', () => {
        stateInput.value = stateInput.value
            .replace(/[^a-zA-Z]/g, '')
            .toUpperCase()
            .slice(0, 2);
    });

    typeInputs.forEach((input) => {
        input.addEventListener('change', updateTypeUI);
    });

    maskPhone(phoneInput);
    maskPhone(whatsappInput);
    updateTypeUI();

    if (zipInput?.value) {
        zipInput.dispatchEvent(new Event('input'));
    }
});
</script>
@endpush
