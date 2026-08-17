import { useForm, Link, Head, router } from '@inertiajs/react';

const statusLabels = { pending: { l: 'Pendente', c: 'bg-yellow-100 text-yellow-800' }, paid: { l: 'Pago', c: 'bg-green-100 text-green-800' }, overdue: { l: 'Vencido', c: 'bg-red-100 text-red-800' }, cancelled: { l: 'Cancelado', c: 'bg-gray-100 text-gray-800' } };

export default function FinancialShow({ entry }) {
    const st = statusLabels[entry.status] || statusLabels.pending;
    function fmt(v) { return Number(v || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }); }

    const payForm = useForm({ paid_amount: entry.amount, paid_date: new Date().toISOString().split('T')[0], payment_method: 'cash' });
    function handlePay(e) { e.preventDefault(); payForm.post(`/financeiro/${entry.id}/pagar`); }

    return (
        <>
            <Head title="Lançamento Financeiro" />
            <div className="space-y-6">
                <div>
                    <Link href="/financeiro" className="text-xs text-[var(--color-primary)] hover:underline">← Voltar</Link>
                    <div className="flex items-center gap-3 mt-1">
                        <h1 className="text-xl font-bold text-[var(--color-text)]">Lançamento Financeiro</h1>
                        <span className={`inline-block px-2 py-0.5 rounded-full text-xs font-medium ${st.c}`}>{st.l}</span>
                        <span className={`inline-block px-2 py-0.5 rounded-full text-xs font-medium ${entry.type === 'receivable' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800'}`}>{entry.type === 'receivable' ? 'A Receber' : 'A Pagar'}</span>
                    </div>
                </div>
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5">
                        <h2 className="text-sm font-semibold text-[var(--color-text)] mb-2">Descrição</h2>
                        <p className="text-sm text-[var(--color-text)]">{entry.description}</p>
                    </div>
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5">
                        <h2 className="text-sm font-semibold text-[var(--color-text)] mb-2">Vencimento</h2>
                        <p className="text-sm text-[var(--color-text)]">{entry.due_date ? new Date(entry.due_date).toLocaleDateString('pt-BR') : '—'}</p>
                    </div>
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5">
                        <h2 className="text-sm font-semibold text-[var(--color-text)] mb-2">Valor</h2>
                        <p className={`text-xl font-bold ${entry.type === 'receivable' ? 'text-[var(--color-success)]' : 'text-[var(--color-danger)]'}`}>{fmt(entry.amount)}</p>
                    </div>
                </div>

                {(entry.status === 'pending' || entry.status === 'overdue') && (
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5 space-y-4">
                        <h2 className="text-sm font-semibold text-[var(--color-text)]">Registrar Pagamento</h2>
                        <form onSubmit={handlePay} className="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                            <div>
                                <label className="block text-xs text-[var(--color-text-muted)] mb-1">Valor Pago</label>
                                <input type="number" value={payForm.data.paid_amount} onChange={(e) => payForm.setData('paid_amount', e.target.value)} step="0.01" min="0.01" className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white" />
                            </div>
                            <div>
                                <label className="block text-xs text-[var(--color-text-muted)] mb-1">Data Pagamento</label>
                                <input type="date" value={payForm.data.paid_date} onChange={(e) => payForm.setData('paid_date', e.target.value)} className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white" />
                            </div>
                            <div>
                                <label className="block text-xs text-[var(--color-text-muted)] mb-1">Forma</label>
                                <select value={payForm.data.payment_method} onChange={(e) => payForm.setData('payment_method', e.target.value)} className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white">
                                    <option value="cash">Dinheiro</option>
                                    <option value="pix">PIX</option>
                                    <option value="credit_card">Crédito</option>
                                    <option value="debit_card">Débito</option>
                                    <option value="boleto">Boleto</option>
                                    <option value="transfer">Transferência</option>
                                </select>
                            </div>
                            <button type="submit" disabled={payForm.processing} className="px-4 py-2 rounded-lg bg-green-600 text-white text-sm font-semibold hover:bg-green-700 transition-colors disabled:opacity-50">Confirmar Pagamento</button>
                        </form>
                    </div>
                )}

                {entry.status === 'paid' && (
                    <div className="bg-green-50 rounded-xl border border-green-200 p-5">
                        <h2 className="text-sm font-semibold text-green-800 mb-2">Pagamento Registrado</h2>
                        <div className="grid grid-cols-3 gap-3 text-sm">
                            <div><span className="text-green-600">Valor pago:</span><p className="font-medium text-green-800">{fmt(entry.paid_amount)}</p></div>
                            <div><span className="text-green-600">Data:</span><p className="font-medium text-green-800">{entry.paid_date ? new Date(entry.paid_date).toLocaleDateString('pt-BR') : '—'}</p></div>
                            <div><span className="text-green-600">Forma:</span><p className="font-medium text-green-800">{entry.payment_method || '—'}</p></div>
                        </div>
                    </div>
                )}

                {entry.notes && (
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5">
                        <h2 className="text-sm font-semibold text-[var(--color-text)] mb-2">Observações</h2>
                        <p className="text-sm text-[var(--color-text-muted)] whitespace-pre-wrap">{entry.notes}</p>
                    </div>
                )}
            </div>
        </>
    );
}
