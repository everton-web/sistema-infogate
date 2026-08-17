import { useForm, Link, Head } from '@inertiajs/react';

export default function CashRegisterOpen() {
    const { data, setData, post, processing, errors } = useForm({
        opening_balance: '',
    });
    function handleSubmit(e) { e.preventDefault(); post('/caixa'); }

    return (
        <>
            <Head title="Abrir Caixa" />
            <div className="space-y-6">
                <div>
                    <Link href="/caixa" className="text-xs text-[var(--color-primary)] hover:underline">← Voltar</Link>
                    <h1 className="text-xl font-bold text-[var(--color-text)] mt-1">Abrir Caixa</h1>
                </div>
                <form onSubmit={handleSubmit} className="max-w-md">
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5 space-y-4">
                        <div>
                            <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Saldo Inicial (R$) *</label>
                            <input type="number" value={data.opening_balance} onChange={(e) => setData('opening_balance', e.target.value)} step="0.01" min="0" className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white" placeholder="0,00" />
                            {errors.opening_balance && <p className="text-xs text-[var(--color-danger)] mt-1">{errors.opening_balance}</p>}
                        </div>
                    </div>
                    <div className="flex gap-3 mt-4">
                        <button type="submit" disabled={processing} className="px-6 py-2.5 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition-colors disabled:opacity-50">{processing ? 'Abrindo...' : 'Abrir Caixa'}</button>
                        <Link href="/caixa" className="px-6 py-2.5 rounded-lg border border-[var(--color-border)] text-sm text-[var(--color-text-muted)] hover:bg-gray-50 transition-colors">Cancelar</Link>
                    </div>
                </form>
            </div>
        </>
    );
}
