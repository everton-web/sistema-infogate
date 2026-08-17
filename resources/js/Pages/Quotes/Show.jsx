import { Link, Head } from '@inertiajs/react';

const statusLabels = { draft: { l: 'Rascunho', c: 'bg-gray-100 text-gray-800' }, sent: { l: 'Enviado', c: 'bg-blue-100 text-blue-800' }, approved: { l: 'Aprovado', c: 'bg-green-100 text-green-800' }, rejected: { l: 'Rejeitado', c: 'bg-red-100 text-red-800' }, expired: { l: 'Expirado', c: 'bg-yellow-100 text-yellow-800' } };

export default function QuoteShow({ quote }) {
    const st = statusLabels[quote.status] || statusLabels.draft;
    function fmt(v) { return Number(v || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }); }

    return (
        <>
            <Head title={`Orçamento #${quote.number}`} />
            <div className="space-y-6">
                <div>
                    <Link href="/orcamentos" className="text-xs text-[var(--color-primary)] hover:underline">← Voltar</Link>
                    <div className="flex items-center gap-3 mt-1">
                        <h1 className="text-xl font-bold text-[var(--color-text)]">Orçamento #{quote.number}</h1>
                        <span className={`inline-block px-2 py-0.5 rounded-full text-xs font-medium ${st.c}`}>{st.l}</span>
                    </div>
                </div>
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5">
                        <h2 className="text-sm font-semibold text-[var(--color-text)] mb-2">Cliente</h2>
                        <p className="text-sm text-[var(--color-text)]">{quote.customer ? <Link href={`/cadastros/clientes/${quote.customer.id}`} className="text-[var(--color-primary)] hover:underline">{quote.customer.name}</Link> : '—'}</p>
                    </div>
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5">
                        <h2 className="text-sm font-semibold text-[var(--color-text)] mb-2">Validade</h2>
                        <p className="text-sm text-[var(--color-text)]">{quote.valid_until ? new Date(quote.valid_until).toLocaleDateString('pt-BR') : 'Sem validade'}</p>
                    </div>
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5">
                        <h2 className="text-sm font-semibold text-[var(--color-text)] mb-2">Total</h2>
                        <p className="text-xl font-bold text-[var(--color-text)]">{fmt(quote.total)}</p>
                    </div>
                </div>
                <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5">
                    <h2 className="text-sm font-semibold text-[var(--color-text)] mb-4">Itens ({quote.items?.length || 0})</h2>
                    {quote.items?.length > 0 ? (
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm">
                                <thead><tr className="border-b border-[var(--color-border)]">
                                    <th className="text-left py-2 font-semibold text-[var(--color-text)]">Descrição</th>
                                    <th className="text-right py-2 font-semibold text-[var(--color-text)]">Qtd</th>
                                    <th className="text-right py-2 font-semibold text-[var(--color-text)]">Valor Unit.</th>
                                    <th className="text-right py-2 font-semibold text-[var(--color-text)]">Total</th>
                                </tr></thead>
                                <tbody>
                                    {quote.items.map((item) => (
                                        <tr key={item.id} className="border-b border-[var(--color-border)] last:border-0">
                                            <td className="py-2 text-[var(--color-text)]">{item.description}</td>
                                            <td className="py-2 text-right text-[var(--color-text-muted)]">{item.quantity}</td>
                                            <td className="py-2 text-right text-[var(--color-text-muted)]">{fmt(item.unit_price)}</td>
                                            <td className="py-2 text-right font-medium text-[var(--color-text)]">{fmt(item.total)}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    ) : <p className="text-sm text-[var(--color-text-muted)]">Nenhum item.</p>}
                    <div className="flex justify-end pt-4 mt-4 border-t border-[var(--color-border)]">
                        <p className="text-xl font-bold text-[var(--color-text)]">Total: {fmt(quote.total)}</p>
                    </div>
                </div>
                {quote.notes && (
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5">
                        <h2 className="text-sm font-semibold text-[var(--color-text)] mb-2">Observações</h2>
                        <p className="text-sm text-[var(--color-text-muted)] whitespace-pre-wrap">{quote.notes}</p>
                    </div>
                )}
            </div>
        </>
    );
}
