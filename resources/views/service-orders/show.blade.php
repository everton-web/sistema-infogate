@extends('layouts.erp')

@section('title', 'OS #' . $order->number)

@push('head')
    <link rel="stylesheet" href="{{ asset('assets/canalsom-modules.css') }}">
@endpush

@section('content')
    <div class="module-heading">
        <div>
            <a href="{{ route('service-orders.index') }}" class="module-back-link">← Voltar para OS</a>
            <h1>Ordem de Serviço #{{ $order->number }}</h1>
            <p>Aberta em {{ $order->opened_at?->format('d/m/Y H:i') ?? '—' }}</p>
        </div>
        <div class="module-actions">
            <span class="status-pill {{ $order->statusColor() }}">{{ $order->statusLabel() }}</span>
        </div>
    </div>

    @if(session('success'))
        <div class="module-alert module-alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="module-alert module-alert-error">
            <strong>Confira os campos informados:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="order-overview-grid">
        <section class="module-card">
            <div class="module-card-header"><h2>Dados da ordem</h2></div>
            <div class="order-details">
                <div><span>Cliente</span><strong>{{ $order->customer?->name ?? '—' }}</strong></div>
                <div>
                    <span>Veículo</span>
                    <strong>
                        @if($order->vehicle)
                            {{ $order->vehicle->plate }} · {{ $order->vehicle->brand?->name }} {{ $order->vehicle->model?->name }}
                        @else
                            —
                        @endif
                    </strong>
                </div>
                <div>
                    <span>Prioridade</span>
                    <strong>{{ ['low' => 'Baixa', 'normal' => 'Normal', 'high' => 'Alta', 'urgent' => 'Urgente'][$order->priority] ?? $order->priority }}</strong>
                </div>
                <div><span>Início</span><strong>{{ $order->started_at?->format('d/m/Y H:i') ?? '—' }}</strong></div>
                <div class="order-detail-full">
                    <span>Reclamação / solicitação</span>
                    <strong>{!! $order->complaint ? nl2br(e($order->complaint)) : '—' !!}</strong>
                </div>
                @if($order->internal_notes)
                    <div class="order-detail-full"><span>Observações internas</span><strong>{!! nl2br(e($order->internal_notes)) !!}</strong></div>
                @endif
            </div>
        </section>

        <div>
            <section class="module-card order-totals-card">
                <div class="module-card-header">
                    <h2>Status da OS</h2>
                    <p>{{ $order->isFinalized() ? 'Esta ordem está finalizada.' : 'Atualize conforme o andamento do serviço.' }}</p>
                </div>
                @if(!$order->isFinalized())
                    <form method="POST" action="{{ route('service-orders.update-status', $order) }}" class="order-status-form">
                        @csrf
                        @method('PATCH')
                        <div class="form-field">
                            <label for="status">Novo status</label>
                            <select id="status" name="status" required>
                                @foreach(['open' => 'Aberta', 'in_progress' => 'Em andamento', 'waiting_parts' => 'Aguardando peças', 'waiting_approval' => 'Aguardando aprovação', 'completed' => 'Concluída', 'delivered' => 'Entregue', 'cancelled' => 'Cancelada'] as $value => $label)
                                    <option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="module-btn module-btn-primary">Atualizar</button>
                    </form>
                @endif
            </section>

            <section class="module-card">
                <div class="module-card-header"><h2>Totais</h2></div>
                <div class="order-totals">
                    <div class="order-total-row"><span>Produtos</span><strong>R$ {{ number_format($order->total_products, 2, ',', '.') }}</strong></div>
                    <div class="order-total-row"><span>Serviços</span><strong>R$ {{ number_format($order->total_services, 2, ',', '.') }}</strong></div>
                    <div class="order-total-row"><span>Desconto</span><strong>R$ {{ number_format($order->discount, 2, ',', '.') }}</strong></div>
                    <div class="order-total-row order-total-main"><span>Total</span><strong>R$ {{ number_format($order->total, 2, ',', '.') }}</strong></div>
                </div>
            </section>
        </div>
    </div>

    <section class="module-card order-items-card">
        <div class="module-card-header">
            <h2>Produtos e serviços</h2>
            <p>{{ $order->items->count() }} {{ $order->items->count() === 1 ? 'item lançado' : 'itens lançados' }}</p>
        </div>
        @if($order->items->isNotEmpty())
            <div class="module-table-scroll">
                <table class="module-table">
                    <thead><tr><th>Descrição</th><th>Tipo</th><th>Quantidade</th><th>Valor unitário</th><th>Desconto</th><th>Total</th>@if(!$order->isFinalized())<th></th>@endif</tr></thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td><strong>{{ $item->description }}</strong></td>
                                <td>{{ $item->type === 'service' ? 'Serviço' : 'Produto' }}</td>
                                <td>{{ number_format($item->quantity, 2, ',', '.') }}</td>
                                <td>R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                <td>R$ {{ number_format($item->discount, 2, ',', '.') }}</td>
                                <td><strong>R$ {{ number_format($item->total, 2, ',', '.') }}</strong></td>
                                @if(!$order->isFinalized())
                                    <td class="cell-right">
                                        <form method="POST" action="{{ route('service-orders.remove-item', [$order, $item]) }}" onsubmit="return confirm('Remover este item da OS?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="order-remove-button">Remover</button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="order-empty-items">Nenhum produto ou serviço lançado nesta OS.</div>
        @endif
    </section>

    @if(!$order->isFinalized())
        <section class="module-card">
            <div class="module-card-header"><h2>Adicionar item</h2><p>Selecione um item do cadastro ou informe a descrição manualmente.</p></div>
            <form method="POST" action="{{ route('service-orders.add-item', $order) }}" class="order-item-form">
                @csrf
                <div class="form-grid">
                    <div class="form-field form-field-full">
                        <label for="product_id">Produto ou serviço cadastrado</label>
                        <select id="product_id" name="product_id">
                            <option value="">Lançamento manual</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-type="{{ $product->type }}" data-description="{{ $product->name }}" data-price="{{ $product->sale_price }}" @selected(old('product_id') == $product->id)>
                                    {{ $product->name }} · R$ {{ number_format($product->sale_price, 2, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-field">
                        <label for="type">Tipo *</label>
                        <select id="type" name="type" required><option value="product" @selected(old('type') === 'product')>Produto</option><option value="service" @selected(old('type') === 'service')>Serviço</option></select>
                    </div>
                    <div class="form-field"><label for="quantity">Quantidade *</label><input id="quantity" name="quantity" type="number" min="0.01" step="0.01" value="{{ old('quantity', '1') }}" required></div>
                    <div class="form-field form-field-full"><label for="description">Descrição *</label><input id="description" name="description" value="{{ old('description') }}" maxlength="255" required></div>
                    <div class="form-field"><label for="unit_price">Valor unitário *</label><input id="unit_price" name="unit_price" type="number" min="0" step="0.01" value="{{ old('unit_price', '0.00') }}" required></div>
                    <div class="form-field"><label for="discount">Desconto</label><input id="discount" name="discount" type="number" min="0" step="0.01" value="{{ old('discount', '0.00') }}"></div>
                </div>
                <div class="form-footer"><button type="submit" class="module-btn module-btn-primary">Adicionar à OS</button></div>
            </form>
        </section>
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const product = document.getElementById('product_id');
            const type = document.getElementById('type');
            const description = document.getElementById('description');
            const price = document.getElementById('unit_price');

            product?.addEventListener('change', () => {
                const option = product.selectedOptions[0];
                if (!option?.value) return;
                type.value = option.dataset.type;
                description.value = option.dataset.description;
                price.value = Number(option.dataset.price || 0).toFixed(2);
            });
        });
    </script>
@endpush
