import { useForm, Link, Head } from '@inertiajs/react';

const typeLabels = { entry: 'Entrada', exit: 'Saída', adjustment: 'Ajuste' };
const typeColors = { entry: 'bg-green-100 text-green-800', exit: 'bg-red-100 text-red-800', adjustment: 'bg-yellow-100 text-yellow-800' };

export default function StockMovements({ product, movements }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        type: 'entry', quantity: '', unit_cost: '', reason: '',
    });

    function handleSubmit(e) {
        e.preventDefault();
        post(`/estoque/${product.id}/movimentacoes`, { onSuccess: () => reset() });
    }

    return (
        <>
            <Head title={`Estoque - ${product.name}`} />
            <div className="space-y-6">
                <div>
                    <Link href="/estoque" className="text-xs text-[var(--color-primary)] hover:underline">← Voltar</Link>
                    <h1 className="text-xl font-bold text-[var(--color-text)] mt-1">{product.name}</h1>
                    <p className="text-sm text-[var(--color-text-muted)]">Estoque atual: <strong>{product.stock_quantity}</strong></p>
                </div>

                <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5 space-y-4">
                    <h2 className="text-sm font-semibold text-[var(--color-text)]">Nova Movimentação</h2>
                    <form onSubmit={handleSubmit} className="grid grid-cols-1 sm:grid-cols-5 gap-3 items-end">
                        <div>
                            <label className="block text-xs text-[var(--color-text-muted)] mb-1">Tipo</label>
                            <select value={data.type} onChange={(e) => setData('type', e.target.value)} className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white">
                                <option value="entry">Entrada</option>
                                <option value="exit">Saída</option>
                                <option value="adjustment">Ajuste</option>
                            </select>
                        </div>
                        <div>
                            <label className="block text-xs text-[var(--color-text-muted)] mb-1">Quantidade</label>
                            <input type="number" value={data.quantity} onChange={(e) => setData('quantity', e.target.value)} step="0.01" min="0.01" className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white" />
                            {errors.quantity && <p className="text-xs text-[var(--color-danger)] mt-1">{errors.quantity}</p>}
                        </div>
                        <div>
                            <label className="block text-xs text-[var(--color-text-muted)] mb-1">Custo Unit. (R$)</label>
                            <input type="number" value={data.unit_cost} onChange={(e) => setData('unit_cost', e.target.value)} step="0.01" min="0" className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white" />
                        </div>
                        <div>
                            <label className="block text-xs text-[var(--color-text-muted)] mb-1">Motivo</label>
                            <input type="text" value={data.reason} onChange={(e) => setData('reason', e.target.value)} className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white" />
                        </div>
                        <button type="submit" disabled={processing} className="px-4 py-2 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition-colors disabled:opacity-50">
                            Registrar
                        </button>
                    </form>
                </div>

                <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="bg-gray-50 border-b border-[var(--color-border)]">
                                    <th className="text-left px-4 py-3 font-semibold text-[var(--color-text)]">Data</th>
                                    <th className="text-center px-4 py-3 font-semibold text-[var(--color-text)]">Tipo</th>
                                    <th className="text-right px-4 py-3 font-semibold text-[var(--color-text)]">Quantidade</th>
                                    <th className="text-right px-4 py-3 font-semibold text-[var(--color-text)]">Custo Unit.</th>
                                    <th className="text-left px-4 py-3 font-semibold text-[var(--color-text)]">Motivo</th>
                                    <th className="text-left px-4 py-3 font-semibold text-[var(--color-text)]">Usuário</th>
                                </tr>
                            </thead>
                            <tbody>
                                {movements.data.length === 0 ? (
                                    <tr><td colSpan={6} className="px-4 py-8 text-center text-[var(--color-text-muted)]">Nenhuma movimentação.</td></tr>
                                ) : (
                                    movements.data.map((m) => (
                                        <tr key={m.id} className="border-b border-[var(--color-border)]">
                                            <td className="px-4 py-3 text-xs text-[var(--color-text-muted)]">{new Date(m.created_at).toLocaleString('pt-BR')}</td>
                                            <td className="px-4 py-3 text-center"><span className={`inline-block px-2 py-0.5 rounded-full text-xs font-medium ${typeColors[m.type]}`}>{typeLabels[m.type]}</span></td>
                                            <td className={`px-4 py-3 text-right font-medium ${m.type === 'exit' ? 'text-[var(--color-danger)]' : 'text-[var(--color-success)]'}`}>
                                                {m.type === 'exit' ? '-' : '+'}{m.quantity}
                                            </td>
                                            <td className="px-4 py-3 text-right text-[var(--color-text-muted)]">
                                                {Number(m.unit_cost) > 0 ? Number(m.unit_cost).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) : '—'}
                                            </td>
                                            <td className="px-4 py-3 text-[var(--color-text-muted)]">{m.reason || '—'}</td>
                                            <td className="px-4 py-3 text-[var(--color-text-muted)]">{m.user?.name || '—'}</td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </>
    );
}
