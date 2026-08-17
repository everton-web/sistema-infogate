import { Link, Head } from '@inertiajs/react';

const statusLabels = { active: { l: 'Ativa', c: 'bg-green-100 text-green-800' }, expired: { l: 'Expirada', c: 'bg-gray-100 text-gray-800' }, claimed: { l: 'Acionada', c: 'bg-yellow-100 text-yellow-800' }, voided: { l: 'Cancelada', c: 'bg-red-100 text-red-800' } };

export default function WarrantyShow({ warranty }) {
    const st = statusLabels[warranty.status] || statusLabels.active;

    return (
        <>
            <Head title="Garantia" />
            <div className="space-y-6">
                <div>
                    <Link href="/garantias" className="text-xs text-[var(--color-primary)] hover:underline">← Voltar</Link>
                    <div className="flex items-center gap-3 mt-1">
                        <h1 className="text-xl font-bold text-[var(--color-text)]">Garantia</h1>
                        <span className={`inline-block px-2 py-0.5 rounded-full text-xs font-medium ${st.c}`}>{st.l}</span>
                    </div>
                </div>
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5 space-y-3">
                        <h2 className="text-sm font-semibold text-[var(--color-text)]">Dados</h2>
                        <div className="grid grid-cols-2 gap-3 text-sm">
                            <div><span className="text-[var(--color-text-muted)]">Cliente:</span><p className="font-medium text-[var(--color-text)]">{warranty.customer ? <Link href={`/cadastros/clientes/${warranty.customer.id}`} className="text-[var(--color-primary)] hover:underline">{warranty.customer.name}</Link> : '—'}</p></div>
                            <div><span className="text-[var(--color-text-muted)]">Produto:</span><p className="font-medium text-[var(--color-text)]">{warranty.product?.name || '—'}</p></div>
                            <div className="col-span-2"><span className="text-[var(--color-text-muted)]">Descrição:</span><p className="font-medium text-[var(--color-text)]">{warranty.description}</p></div>
                        </div>
                    </div>
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5 space-y-3">
                        <h2 className="text-sm font-semibold text-[var(--color-text)]">Período</h2>
                        <div className="grid grid-cols-2 gap-3 text-sm">
                            <div><span className="text-[var(--color-text-muted)]">Início:</span><p className="font-medium text-[var(--color-text)]">{warranty.start_date ? new Date(warranty.start_date).toLocaleDateString('pt-BR') : '—'}</p></div>
                            <div><span className="text-[var(--color-text-muted)]">Fim:</span><p className="font-medium text-[var(--color-text)]">{warranty.end_date ? new Date(warranty.end_date).toLocaleDateString('pt-BR') : '—'}</p></div>
                        </div>
                    </div>
                </div>
                {warranty.notes && (
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5">
                        <h2 className="text-sm font-semibold text-[var(--color-text)] mb-2">Observações</h2>
                        <p className="text-sm text-[var(--color-text-muted)] whitespace-pre-wrap">{warranty.notes}</p>
                    </div>
                )}
            </div>
        </>
    );
}
