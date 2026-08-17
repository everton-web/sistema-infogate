import { useForm, Link, Head } from '@inertiajs/react';

export default function FinancialCreate({ customers, suppliers }) {
    const { data, setData, post, processing, errors } = useForm({
        type: 'receivable', description: '', amount: '', due_date: '', category: '', notes: '',
    });
    function handleSubmit(e) { e.preventDefault(); post('/financeiro'); }

    return (
        <>
            <Head title="Novo Lançamento" />
            <div className="space-y-6">
                <div>
                    <Link href="/financeiro" className="text-xs text-[var(--color-primary)] hover:underline">← Voltar</Link>
                    <h1 className="text-xl font-bold text-[var(--color-text)] mt-1">Novo Lançamento Financeiro</h1>
                </div>
                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5 space-y-4">
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Tipo *</label>
                                <select value={data.type} onChange={(e) => setData('type', e.target.value)} className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white">
                                    <option value="receivable">A Receber</option>
                                    <option value="payable">A Pagar</option>
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Categoria</label>
                                <input type="text" value={data.category} onChange={(e) => setData('category', e.target.value)} className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white" placeholder="Ex: Serviço, Material" />
                            </div>
                            <div className="sm:col-span-2">
                                <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Descrição *</label>
                                <input type="text" value={data.description} onChange={(e) => setData('description', e.target.value)} className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white" />
                                {errors.description && <p className="text-xs text-[var(--color-danger)] mt-1">{errors.description}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Valor (R$) *</label>
                                <input type="number" value={data.amount} onChange={(e) => setData('amount', e.target.value)} step="0.01" min="0.01" className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white" />
                                {errors.amount && <p className="text-xs text-[var(--color-danger)] mt-1">{errors.amount}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Vencimento *</label>
                                <input type="date" value={data.due_date} onChange={(e) => setData('due_date', e.target.value)} className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white" />
                                {errors.due_date && <p className="text-xs text-[var(--color-danger)] mt-1">{errors.due_date}</p>}
                            </div>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Observações</label>
                            <textarea value={data.notes} onChange={(e) => setData('notes', e.target.value)} rows={2} className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white" />
                        </div>
                    </div>
                    <div className="flex gap-3">
                        <button type="submit" disabled={processing} className="px-6 py-2.5 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition-colors disabled:opacity-50">{processing ? 'Salvando...' : 'Salvar Lançamento'}</button>
                        <Link href="/financeiro" className="px-6 py-2.5 rounded-lg border border-[var(--color-border)] text-sm text-[var(--color-text-muted)] hover:bg-gray-50 transition-colors">Cancelar</Link>
                    </div>
                </form>
            </div>
        </>
    );
}
