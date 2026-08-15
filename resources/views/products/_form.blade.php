<div class="form-section">
    <div class="form-section-heading">
        <div>
            <span class="form-step">1</span>
            <div>
                <h2>Identificação</h2>
                <p>Dados básicos do produto ou serviço.</p>
            </div>
        </div>
    </div>

    <div class="form-grid">
        <div class="form-field">
            <label for="type">Tipo *</label>
            <select name="type" id="type" required>
                <option value="product" @selected(old('type', $product->type ?? 'product') === 'product')>Produto</option>
                <option value="service" @selected(old('type', $product->type ?? 'product') === 'service')>Serviço</option>
            </select>
        </div>

        <div class="form-field">
            <label for="unit">Unidade *</label>
            <select name="unit" id="unit" required>
                <option value="un" @selected(old('unit', $product->unit ?? 'un') === 'un')>Unidade (un)</option>
                <option value="pç" @selected(old('unit', $product->unit ?? 'un') === 'pç')>Peça (pç)</option>
                <option value="m" @selected(old('unit', $product->unit ?? 'un') === 'm')>Metro (m)</option>
                <option value="kg" @selected(old('unit', $product->unit ?? 'un') === 'kg')>Quilo (kg)</option>
                <option value="hr" @selected(old('unit', $product->unit ?? 'un') === 'hr')>Hora (hr)</option>
                <option value="sv" @selected(old('unit', $product->unit ?? 'un') === 'sv')>Serviço (sv)</option>
            </select>
        </div>

        <div class="form-field form-field-full">
            <label for="name">Nome *</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $product->name ?? '') }}"
                required
            >
        </div>

        <div class="form-field">
            <label for="sku">Código / SKU</label>
            <input
                type="text"
                id="sku"
                name="sku"
                maxlength="50"
                value="{{ old('sku', $product->sku ?? '') }}"
            >
        </div>

        <div class="form-field">
            <label for="barcode">Código de barras</label>
            <input
                type="text"
                id="barcode"
                name="barcode"
                maxlength="50"
                value="{{ old('barcode', $product->barcode ?? '') }}"
            >
        </div>

        <div class="form-field form-field-full">
            <label for="description">Descrição</label>
            <textarea
                id="description"
                name="description"
                rows="3"
                placeholder="Descrição detalhada do produto ou serviço..."
            >{{ old('description', $product->description ?? '') }}</textarea>
        </div>
    </div>
</div>

<div class="form-section">
    <div class="form-section-heading">
        <div>
            <span class="form-step">2</span>
            <div>
                <h2>Preços e estoque</h2>
                <p>Valores e controle de quantidade.</p>
            </div>
        </div>
    </div>

    <div class="form-grid">
        <div class="form-field">
            <label for="cost_price">Preço de custo (R$)</label>
            <input
                type="number"
                id="cost_price"
                name="cost_price"
                min="0"
                step="0.01"
                value="{{ old('cost_price', $product->cost_price ?? '0.00') }}"
            >
        </div>

        <div class="form-field">
            <label for="sale_price">Preço de venda (R$)</label>
            <input
                type="number"
                id="sale_price"
                name="sale_price"
                min="0"
                step="0.01"
                value="{{ old('sale_price', $product->sale_price ?? '0.00') }}"
            >
        </div>

        <div class="form-field" id="stock-quantity-field">
            <label for="stock_quantity">Quantidade em estoque</label>
            <input
                type="number"
                id="stock_quantity"
                name="stock_quantity"
                min="0"
                step="0.01"
                value="{{ old('stock_quantity', $product->stock_quantity ?? '0') }}"
            >
        </div>

        <div class="form-field" id="stock-minimum-field">
            <label for="stock_minimum">Estoque mínimo</label>
            <input
                type="number"
                id="stock_minimum"
                name="stock_minimum"
                min="0"
                step="0.01"
                value="{{ old('stock_minimum', $product->stock_minimum ?? '0') }}"
            >
        </div>

        <div class="form-field">
            <label for="status">Situação *</label>
            <select name="status" id="status" required>
                <option value="active" @selected(old('status', $product->status ?? 'active') === 'active')>Ativo</option>
                <option value="inactive" @selected(old('status', $product->status ?? 'active') === 'inactive')>Inativo</option>
            </select>
        </div>
    </div>
</div>
