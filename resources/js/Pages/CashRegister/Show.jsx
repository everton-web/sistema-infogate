import { useForm, Link, Head } from '@inertiajs/react';

export default function CashRegisterShow({ register }) {
    function fmt(v) { return Number(v || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }); }
    const isOpen = register.status === 'open';

    const closeForm = useForm({ closing_balance: '' });
    function handleClose(e) { e.preventDefault(); closeForm.post(`/caixa/${register.id}/fechar`); }

    return (
        <>
            <Head title="Caixa" />
            <div className="space-y-6">
                <div>
                    <Link href="/caixa" className="text-xs text-[var(--color-primary)] hover:underline">← Voltar</Link>
                    <div className="flex items-center gap-3 mt-1">
                        <h1 className="text-xl font-bold text-[var(--color-text)]">Caixa</h1>
                        <span className={`inline-block px-2 py-0.5 rounded-full text-xs font-medium ${isOpen ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}`}>
                            {isOpen ? 'Aberto' : 'Fechado'}
                        </span>
                    </div>
                </div>
                <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5">
                        <h2 className="text-sm font-semibold text-[var(--color-text)] mb-2">Operador</h2>
                        <p className="text-sm text-[var(--color-text)]">{register.user?.name || '—'}</p>
                    </div>
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5">
                        <h2 className="text-sm font-semibold text-[var(--color-text)] mb-2">Saldo Inicial</h2>
                        <p className="text-lg font-bold text-[var(--color-text)]">{fmt(register.opening_balance)}</p>
                    </div>
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5">
                        <h2 className="text-sm font-semibold text-[var(--color-text)] mb-2">Aberto em</h2>
                        <p className="text-sm text-[var(--color-text)]">{register.opened_at ? new Date(register.opened_at).toLocaleString('pt-BR') : '—'}</p>
                    </div>
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5">
                        <h2 className="text-sm font-semibold text-[var(--color-text)] mb-2">{isOpen ? 'Saldo Atual' : 'Saldo Final'}</h2>
                        <p className="text-lg font-bold text-[var(--color-text)]">{register.closing_balance ? fmt(register.closing_balance) : '—'}</p>
                    </div>
                </div>

                {isOpen && (
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5 space-y-4">
                        <h2 className="text-sm font-semibold text-[var(--color-text)]">Fechar Caixa</h2>
                        <form onSubmit={handleClose} className="flex items-end gap-3">
                            <div>
                                <label className="block text-xs text-[var(--color-text-muted)] mb-1">Saldo Final (R$)</label>
                                <input type="number" value={closeForm.data.closing_balance} onChange={(e) => closeForm.setData('closing_balance', e.target.value)} step="0.01" min="0" className="px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white" />
                            </div>
                            <button type="submit" disabled={closeForm.processing} className="px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-semibold hover:bg-red-700 transition-colors disabled:opacity-50">Fechar Caixa</button>
                        </form>
                    </div>
                )}

                {!isOpen && register.closed_at && (
                    <div className="bg-gray-50 rounded-xl border border-gray-200 p-5">
                        <p className="text-sm text-[var(--color-text-muted)]">Caixa fechado em {new Date(register.closed_at).toLocaleString('pt-BR')}</p>
                    </div>
                )}
            </div>
        </>
    );
}
