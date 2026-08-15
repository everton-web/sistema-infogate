@php
    $editing = isset($supplier);
    $type = old('type', $supplier->type ?? 'pj');
    $statusValue = old('status', $supplier->status ?? 'active');

    $documentRaw = old('document', $supplier->document ?? '');
    $phoneRaw = old('phone', $supplier->phone ?? '');
    $whatsappRaw = old('whatsapp', $supplier->whatsapp ?? '');
@endphp

<div class="form-section">
    <div class="form-section-heading">
        <div>
            <span class="form-step">1</span>
            <div>
                <h2>Identificação</h2>
                <p>Dados principais do fornecedor.</p>
            </div>
        </div>
    </div>

    <div class="form-grid">
        <div class="form-field">
            <label for="type">Tipo *</label>
            <select name="type" id="type" required>
                <option value="pj" @selected($type === 'pj')>Pessoa Jurídica</option>
                <option value="pf" @selected($type === 'pf')>Pessoa Física</option>
            </select>
        </div>

        <div class="form-field">
            <label for="status">Situação *</label>
            <select id="status" name="status" required>
                <option value="active" @selected($statusValue === 'active')>Ativo</option>
                <option value="inactive" @selected($statusValue === 'inactive')>Inativo</option>
            </select>
        </div>

        <div class="form-field form-field-full">
            <label for="name" id="nameLabel">Razão social *</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $supplier->name ?? '') }}"
                required
            >
        </div>

        <div class="form-field">
            <label for="document" id="documentLabel">CNPJ</label>
            <input
                type="text"
                id="document"
                name="document"
                value="{{ $documentRaw }}"
                inputmode="numeric"
            >
        </div>

        <div class="form-field pj-only">
            <label for="trade_name">Nome fantasia</label>
            <input
                type="text"
                id="trade_name"
                name="trade_name"
                value="{{ old('trade_name', $supplier->trade_name ?? '') }}"
            >
        </div>

        <div class="form-field pj-only">
            <label for="state_registration">Inscrição estadual</label>
            <input
                type="text"
                id="state_registration"
                name="state_registration"
                value="{{ old('state_registration', $supplier->state_registration ?? '') }}"
            >
        </div>

        <div class="form-field">
            <label for="contact_person">Pessoa de contato</label>
            <input
                type="text"
                id="contact_person"
                name="contact_person"
                value="{{ old('contact_person', $supplier->contact_person ?? '') }}"
            >
        </div>
    </div>
</div>

<div class="form-section">
    <div class="form-section-heading">
        <div>
            <span class="form-step">2</span>
            <div>
                <h2>Contato</h2>
                <p>Telefone, WhatsApp e e-mail.</p>
            </div>
        </div>
    </div>

    <div class="form-grid">
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

        <div class="form-field form-field-full">
            <label for="email">E-mail</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email', $supplier->email ?? '') }}"
            >
        </div>
    </div>
</div>

<div class="form-section">
    <div class="form-section-heading">
        <div>
            <span class="form-step">3</span>
            <div>
                <h2>Endereço</h2>
                <p>Informações opcionais.</p>
            </div>
        </div>
    </div>

    <div class="form-grid">
        <div class="form-field">
            <label for="postal_code">CEP</label>
            <input
                type="text"
                id="postal_code"
                name="postal_code"
                maxlength="9"
                value="{{ old('postal_code', $supplier->postal_code ?? '') }}"
                placeholder="00000-000"
                inputmode="numeric"
            >
        </div>

        <div class="form-field form-field-full">
            <label for="street">Logradouro</label>
            <input
                type="text"
                id="street"
                name="street"
                value="{{ old('street', $supplier->street ?? '') }}"
            >
        </div>

        <div class="form-field">
            <label for="number">Número</label>
            <input
                type="text"
                id="number"
                name="number"
                value="{{ old('number', $supplier->number ?? '') }}"
            >
        </div>

        <div class="form-field">
            <label for="complement">Complemento</label>
            <input
                type="text"
                id="complement"
                name="complement"
                value="{{ old('complement', $supplier->complement ?? '') }}"
            >
        </div>

        <div class="form-field">
            <label for="neighborhood">Bairro</label>
            <input
                type="text"
                id="neighborhood"
                name="neighborhood"
                value="{{ old('neighborhood', $supplier->neighborhood ?? '') }}"
            >
        </div>

        <div class="form-field">
            <label for="city">Cidade</label>
            <input
                type="text"
                id="city"
                name="city"
                value="{{ old('city', $supplier->city ?? '') }}"
            >
        </div>

        <div class="form-field">
            <label for="state">UF</label>
            <input
                type="text"
                id="state"
                name="state"
                maxlength="2"
                value="{{ old('state', $supplier->state ?? 'BA') }}"
                placeholder="BA"
            >
        </div>
    </div>
</div>

<div class="form-section">
    <div class="form-section-heading">
        <div>
            <span class="form-step">4</span>
            <div>
                <h2>Observações</h2>
                <p>Anotações internas.</p>
            </div>
        </div>
    </div>

    <div class="form-grid">
        <div class="form-field form-field-full">
            <textarea
                id="notes"
                name="notes"
                rows="4"
                placeholder="Informações adicionais..."
            >{{ old('notes', $supplier->notes ?? '') }}</textarea>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const typeSelect = document.getElementById('type');
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
        if (value.length > 2) value = '(' + value.slice(0, 2) + ')' + value.slice(2);
        if (value.length > 9) value = value.slice(0, 9) + '-' + value.slice(9);
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

    function updateTypeUI() {
        const isPJ = typeSelect.value === 'pj';
        document.querySelectorAll('.pj-only').forEach(el => {
            el.style.display = isPJ ? '' : 'none';
        });
        nameLabel.textContent = isPJ ? 'Razão social *' : 'Nome *';
        documentLabel.textContent = isPJ ? 'CNPJ' : 'CPF';
        documentInput.placeholder = isPJ ? '00.000.000/0000-00' : '000.000.000-00';
        documentInput.maxLength = isPJ ? 18 : 14;
        documentInput.value = isPJ ? maskCnpj(documentInput.value) : maskCpf(documentInput.value);
    }

    documentInput?.addEventListener('input', () => {
        documentInput.value = typeSelect.value === 'pj'
            ? maskCnpj(documentInput.value)
            : maskCpf(documentInput.value);
    });

    phoneInput?.addEventListener('input', () => maskPhone(phoneInput));
    whatsappInput?.addEventListener('input', () => maskPhone(whatsappInput));

    zipInput?.addEventListener('input', () => {
        let value = digits(zipInput.value, 8);
        if (value.length > 5) value = value.slice(0, 5) + '-' + value.slice(5);
        zipInput.value = value;
    });

    stateInput?.addEventListener('input', () => {
        stateInput.value = stateInput.value.replace(/[^a-zA-Z]/g, '').toUpperCase().slice(0, 2);
    });

    typeSelect.addEventListener('change', updateTypeUI);
    maskPhone(phoneInput);
    maskPhone(whatsappInput);
    updateTypeUI();
});
</script>
@endpush
