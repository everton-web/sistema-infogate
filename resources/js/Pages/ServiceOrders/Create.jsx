import { useForm, Link, Head } from '@inertiajs/react';
import { useState } from 'react';

export default function ServiceOrderCreate({ customers, products }) {
    const [vehicles, setVehicles] = useState([]);
    const [loadingVehicles, setLoadingVehicles] = useState(false);

    const { data, setData, post, processing, errors } = useForm({
        customer_id: '',
        vehicle_id: '',
        complaint: '',
        diagnosis: '',
        internal_notes: '',
        discount: 0,
        items: [],
    });

    async function handleCustomerChange(customerId) {
        setData('customer_id', customerId);
        setData('vehicle_id', '');
        setVehicles([]);

        if (customerId) {
            setLoadingVehicles(true);
            try {
                const response = await fetch(`/api/clientes/${customerId}/veiculos`);
                const data = await response.json();
                setVehicles(data);
            } catch {
                setVehicles([]);
            }
            setLoadingVehicles(false);
        }
    }

    function addItem() {
        setData('items', [...data.items, {
            product_id: '', type: 'service', description: '', quantity: 1, unit_price: 0, discount: 0,
        }]);
    }

    function updateItem(index, field, value) {
        const items = [...data.items];
        items[index] = { ...items[index], [field]: value };

        if (field === 'product_id' && value) {
            const product = products.find((p) => p.id === Number(value));
            if (product) {
                items[index].description = product.name;
                items[index].type = product.type;
                items[index].unit_price = Number(product.sale_price);
            }
        }

        setData('items', items);
    }

    function removeItem(index) {
        setData('items', data.items.filter((_, i) => i !== index));
    }

    function itemTotal(item) {
        return Math.max(0, (item.quantity * item.unit_price) - (item.discount || 0));
    }

    function subtotal() {
        return data.items.reduce((sum, item) => sum + itemTotal(item), 0);
    }

    function total() {
        return Math.max(0, subtotal() - (Number(data.discount) || 0));
    }

    function formatCurrency(value) {
        return Number(value || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }

    function handleSubmit(e) {
        e.preventDefault();
        post('/ordens-servico');
    }

    return (
        <>
            <Head title="Nova Ordem de Serviço" />
            <div className="space-y-6">
                <div>
                    <Link href="/ordens-servico" className="text-xs text-[var(--color-primary)] hover:underline">← Voltar</Link>
                    <h1 className="text-xl font-bold text-[var(--color-text)] mt-1">Nova Ordem de Serviço</h1>
                </div>

                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5 space-y-4">
                        <h2 className="text-sm font-semibold text-[var(--color-text)]">Cliente e Veículo</h2>
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Cliente</label>
                                <select value={data.customer_id} onChange={(e) => handleCustomerChange(e.target.value)} className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white">
                                    <option value="">Selecione...</option>
                                    {customers.map((c) => <option key={c.id} value={c.id}>{c.name}</option>)}
                                </select>
                                {errors.customer_id && <p className="text-xs text-[var(--color-danger)] mt-1">{errors.customer_id}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Veículo</label>
                                <select value={data.vehicle_id} onChange={(e) => setData('vehicle_id', e.target.value)} disabled={!data.customer_id || loadingVehicles} className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white disabled:opacity-50">
                                    <option value="">{loadingVehicles ? 'Carregando...' : 'Selecione (opcional)...'}</option>
                                    {vehicles.map((v) => <option key={v.id} value={v.id}>{v.label}</option>)}
                                </select>
                            </div>
                        </div>
                    </div>

                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5 space-y-4">
                        <h2 className="text-sm font-semibold text-[var(--color-text)]">Detalhes</h2>
                        <div className="space-y-4">
                            <div>
                                <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Reclamação do Cliente</label>
                                <textarea value={data.complaint} onChange={(e) => setData('complaint', e.target.value)} rows={2} className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Diagnóstico</label>
                                <textarea value={data.diagnosis} onChange={(e) => setData('diagnosis', e.target.value)} rows={2} className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Observações Internas</label>
                                <textarea value={data.internal_notes} onChange={(e) => setData('internal_notes', e.target.value)} rows={2} className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]" />
                            </div>
                        </div>
                    </div>

                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5 space-y-4">
                        <div className="flex items-center justify-between">
                            <h2 className="text-sm font-semibold text-[var(--color-text)]">Itens ({data.items.length})</h2>
                            <button type="button" onClick={addItem} className="text-xs text-[var(--color-primary)] hover:underline">+ Adicionar item</button>
                        </div>

                        {data.items.length === 0 ? (
                            <p className="text-sm text-[var(--color-text-muted)]">Nenhum item adicionado.</p>
                        ) : (
                            <div className="space-y-3">
                                {data.items.map((item, i) => (
                                    <div key={i} className="grid grid-cols-12 gap-2 items-end p-3 bg-gray-50 rounded-lg">
                                        <div className="col-span-12 sm:col-span-3">
                                            <label className="block text-xs text-[var(--color-text-muted)] mb-1">Produto/Serviço</label>
                                            <select value={item.product_id} onChange={(e) => updateItem(i, 'product_id', e.target.value)} className="w-full px-2 py-1.5 rounded border border-[var(--color-border)] text-xs bg-white">
                                                <option value="">Manual</option>
                                                {products.map((p) => <option key={p.id} value={p.id}>{p.name}</option>)}
                                            </select>
                                        </div>
                                        <div className="col-span-12 sm:col-span-3">
                                            <label className="block text-xs text-[var(--color-text-muted)] mb-1">Descrição</label>
                                            <input type="text" value={item.description} onChange={(e) => updateItem(i, 'description', e.target.value)} className="w-full px-2 py-1.5 rounded border border-[var(--color-border)] text-xs bg-white" />
                                        </div>
                                        <div className="col-span-4 sm:col-span-1">
                                            <label className="block text-xs text-[var(--color-text-muted)] mb-1">Qtd</label>
                                            <input type="number" value={item.quantity} onChange={(e) => updateItem(i, 'quantity', Number(e.target.value))} step="0.01" min="0.01" className="w-full px-2 py-1.5 rounded border border-[var(--color-border)] text-xs bg-white" />
                                        </div>
                                        <div className="col-span-4 sm:col-span-2">
                                            <label className="block text-xs text-[var(--color-text-muted)] mb-1">Valor Unit.</label>
                                            <input type="number" value={item.unit_price} onChange={(e) => updateItem(i, 'unit_price', Number(e.target.value))} step="0.01" min="0" className="w-full px-2 py-1.5 rounded border border-[var(--color-border)] text-xs bg-white" />
                                        </div>
                                        <div className="col-span-3 sm:col-span-2">
                                            <label className="block text-xs text-[var(--color-text-muted)] mb-1">Subtotal</label>
                                            <span className="block text-xs font-medium text-[var(--color-text)] py-1.5">{formatCurrency(itemTotal(item))}</span>
                                        </div>
                                        <div className="col-span-1">
                                            <button type="button" onClick={() => removeItem(i)} className="text-[var(--color-danger)] text-xs hover:underline">X</button>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}

                        <div className="flex justify-end pt-3 border-t border-[var(--color-border)]">
                            <div className="text-right space-y-1">
                                <p className="text-sm text-[var(--color-text-muted)]">Subtotal: {formatCurrency(subtotal())}</p>
                                <div className="flex items-center gap-2">
                                    <label className="text-sm text-[var(--color-text-muted)]">Desconto (R$):</label>
                                    <input type="number" value={data.discount} onChange={(e) => setData('discount', Number(e.target.value))} step="0.01" min="0" className="w-24 px-2 py-1 rounded border border-[var(--color-border)] text-sm bg-white text-right" />
                                </div>
                                <p className="text-lg font-bold text-[var(--color-text)]">Total: {formatCurrency(total())}</p>
                            </div>
                        </div>
                    </div>

                    <div className="flex gap-3">
                        <button type="submit" disabled={processing} className="px-6 py-2.5 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition-colors disabled:opacity-50">
                            {processing ? 'Criando...' : 'Criar Ordem de Serviço'}
                        </button>
                        <Link href="/ordens-servico" className="px-6 py-2.5 rounded-lg border border-[var(--color-border)] text-sm text-[var(--color-text-muted)] hover:bg-gray-50 transition-colors">Cancelar</Link>
                    </div>
                </form>
            </div>
        </>
    );
}
