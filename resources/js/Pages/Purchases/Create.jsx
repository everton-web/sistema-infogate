import { useForm, Link, Head } from '@inertiajs/react';

export default function PurchaseCreate({ suppliers, products }) {
    const { data, setData, post, processing, errors } = useForm({
        supplier_id: '', discount: 0, shipping: 0, notes: '', items: [],
    });

    function addItem() { setData('items', [...data.items, { product_id: '', description: '', quantity: 1, unit_cost: 0 }]); }
    function removeItem(i) { setData('items', data.items.filter((_, idx) => idx !== i)); }
    function updateItem(i, field, value) {
        const items = [...data.items];
        items[i] = { ...items[i], [field]: value };
        if (field === 'product_id' && value) {
            const p = products.find((p) => p.id === Number(value));
            if (p) { items[i].description = p.name; items[i].unit_cost = Number(p.cost_price || 0); }
        }
        setData('items', items);
    }
    function itemTotal(it) { return it.quantity * it.unit_cost; }
    function subtotal() { return data.items.reduce((s, it) => s + itemTotal(it), 0); }
    function total() { return Math.max(0, subtotal() - (Number(data.discount) || 0) + (Number(data.shipping) || 0)); }
    function fmt(v) { return Number(v || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }); }
    function handleSubmit(e) { e.preventDefault(); post('/compras'); }

    return (
        <>
            <Head title="Nova Compra" />
            <div className="space-y-6">
                <div>
                    <Link href="/compras" className="text-xs text-[var(--color-primary)] hover:underline">← Voltar</Link>
                    <h1 className="text-xl font-bold text-[var(--color-text)] mt-1">Nova Compra</h1>
                </div>
                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5 space-y-4">
                        <h2 className="text-sm font-semibold text-[var(--color-text)]">Dados da Compra</h2>
                        <div>
                            <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Fornecedor *</label>
                            <select value={data.supplier_id} onChange={(e) => setData('supplier_id', e.target.value)} className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white">
                                <option value="">Selecione...</option>
                                {suppliers.map((s) => <option key={s.id} value={s.id}>{s.name}</option>)}
                            </select>
                            {errors.supplier_id && <p className="text-xs text-[var(--color-danger)] mt-1">{errors.supplier_id}</p>}
                        </div>
                    </div>
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5 space-y-4">
                        <div className="flex items-center justify-between">
                            <h2 className="text-sm font-semibold text-[var(--color-text)]">Itens ({data.items.length})</h2>
                            <button type="button" onClick={addItem} className="text-xs text-[var(--color-primary)] hover:underline">+ Adicionar</button>
                        </div>
                        {data.items.length === 0 ? <p className="text-sm text-[var(--color-text-muted)]">Nenhum item.</p> : (
                            <div className="space-y-3">
                                {data.items.map((item, i) => (
                                    <div key={i} className="grid grid-cols-12 gap-2 items-end p-3 bg-gray-50 rounded-lg">
                                        <div className="col-span-12 sm:col-span-4">
                                            <label className="block text-xs text-[var(--color-text-muted)] mb-1">Produto</label>
                                            <select value={item.product_id} onChange={(e) => updateItem(i, 'product_id', e.target.value)} className="w-full px-2 py-1.5 rounded border border-[var(--color-border)] text-xs bg-white">
                                                <option value="">Manual</option>
                                                {products.map((p) => <option key={p.id} value={p.id}>{p.name}</option>)}
                                            </select>
                                        </div>
                                        <div className="col-span-12 sm:col-span-3">
                                            <label className="block text-xs text-[var(--color-text-muted)] mb-1">Descrição</label>
                                            <input type="text" value={item.description} onChange={(e) => updateItem(i, 'description', e.target.value)} className="w-full px-2 py-1.5 rounded border border-[var(--color-border)] text-xs bg-white" />
                                        </div>
                                        <div className="col-span-4 sm:col-span-1"><label className="block text-xs text-[var(--color-text-muted)] mb-1">Qtd</label><input type="number" value={item.quantity} onChange={(e) => updateItem(i, 'quantity', Number(e.target.value))} step="1" min="1" className="w-full px-2 py-1.5 rounded border border-[var(--color-border)] text-xs bg-white" /></div>
                                        <div className="col-span-4 sm:col-span-2"><label className="block text-xs text-[var(--color-text-muted)] mb-1">Custo Unit.</label><input type="number" value={item.unit_cost} onChange={(e) => updateItem(i, 'unit_cost', Number(e.target.value))} step="0.01" min="0" className="w-full px-2 py-1.5 rounded border border-[var(--color-border)] text-xs bg-white" /></div>
                                        <div className="col-span-3 sm:col-span-1"><span className="block text-xs font-medium text-[var(--color-text)] py-1.5">{fmt(itemTotal(item))}</span></div>
                                        <div className="col-span-1"><button type="button" onClick={() => removeItem(i)} className="text-[var(--color-danger)] text-xs hover:underline">X</button></div>
                                    </div>
                                ))}
                            </div>
                        )}
                        <div className="flex justify-end pt-3 border-t border-[var(--color-border)]">
                            <div className="text-right space-y-1">
                                <p className="text-sm text-[var(--color-text-muted)]">Subtotal: {fmt(subtotal())}</p>
                                <div className="flex items-center gap-2"><label className="text-sm text-[var(--color-text-muted)]">Desconto:</label><input type="number" value={data.discount} onChange={(e) => setData('discount', Number(e.target.value))} step="0.01" min="0" className="w-24 px-2 py-1 rounded border border-[var(--color-border)] text-sm bg-white text-right" /></div>
                                <div className="flex items-center gap-2"><label className="text-sm text-[var(--color-text-muted)]">Frete:</label><input type="number" value={data.shipping} onChange={(e) => setData('shipping', Number(e.target.value))} step="0.01" min="0" className="w-24 px-2 py-1 rounded border border-[var(--color-border)] text-sm bg-white text-right" /></div>
                                <p className="text-lg font-bold text-[var(--color-text)]">Total: {fmt(total())}</p>
                            </div>
                        </div>
                    </div>
                    <div className="flex gap-3">
                        <button type="submit" disabled={processing} className="px-6 py-2.5 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition-colors disabled:opacity-50">{processing ? 'Salvando...' : 'Salvar Compra'}</button>
                        <Link href="/compras" className="px-6 py-2.5 rounded-lg border border-[var(--color-border)] text-sm text-[var(--color-text-muted)] hover:bg-gray-50 transition-colors">Cancelar</Link>
                    </div>
                </form>
            </div>
        </>
    );
}
