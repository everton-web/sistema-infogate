import { useForm, Link, Head } from '@inertiajs/react';

export default function WarrantyCreate({ customers, products }) {
    const { data, setData, post, processing, errors } = useForm({
        customer_id: '', product_id: '', description: '', start_date: '', end_date: '', notes: '',
    });
    function handleSubmit(e) { e.preventDefault(); post('/garantias'); }

    return (
        <>
            <Head title="Nova Garantia" />
            <div className="space-y-6">
                <div>
                    <Link href="/garantias" className="text-xs text-[var(--color-primary)] hover:underline">← Voltar</Link>
                    <h1 className="text-xl font-bold text-[var(--color-text)] mt-1">Nova Garantia</h1>
                </div>
                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5 space-y-4">
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Cliente *</label>
                                <select value={data.customer_id} onChange={(e) => setData('customer_id', e.target.value)} className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white">
                                    <option value="">Selecione...</option>
                                    {customers.map((c) => <option key={c.id} value={c.id}>{c.name}</option>)}
                                </select>
                                {errors.customer_id && <p className="text-xs text-[var(--color-danger)] mt-1">{errors.customer_id}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Produto</label>
                                <select value={data.product_id} onChange={(e) => setData('product_id', e.target.value)} className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white">
                                    <option value="">Selecione...</option>
                                    {products.map((p) => <option key={p.id} value={p.id}>{p.name}</option>)}
                                </select>
                            </div>
                            <div className="sm:col-span-2">
                                <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Descrição *</label>
                                <input type="text" value={data.description} onChange={(e) => setData('description', e.target.value)} className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white" placeholder="Descrição do item em garantia" />
                                {errors.description && <p className="text-xs text-[var(--color-danger)] mt-1">{errors.description}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Data Início *</label>
                                <input type="date" value={data.start_date} onChange={(e) => setData('start_date', e.target.value)} className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white" />
                                {errors.start_date && <p className="text-xs text-[var(--color-danger)] mt-1">{errors.start_date}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Data Fim *</label>
                                <input type="date" value={data.end_date} onChange={(e) => setData('end_date', e.target.value)} className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white" />
                                {errors.end_date && <p className="text-xs text-[var(--color-danger)] mt-1">{errors.end_date}</p>}
                            </div>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Observações</label>
                            <textarea value={data.notes} onChange={(e) => setData('notes', e.target.value)} rows={3} className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white" />
                        </div>
                    </div>
                    <div className="flex gap-3">
                        <button type="submit" disabled={processing} className="px-6 py-2.5 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition-colors disabled:opacity-50">{processing ? 'Salvando...' : 'Salvar Garantia'}</button>
                        <Link href="/garantias" className="px-6 py-2.5 rounded-lg border border-[var(--color-border)] text-sm text-[var(--color-text-muted)] hover:bg-gray-50 transition-colors">Cancelar</Link>
                    </div>
                </form>
            </div>
        </>
    );
}
