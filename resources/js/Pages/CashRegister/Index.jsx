import { Link, Head } from '@inertiajs/react';

const statusLabels = { open: { l: 'Aberto', c: 'bg-green-100 text-green-800' }, closed: { l: 'Fechado', c: 'bg-gray-100 text-gray-800' } };

export default function CashRegisterIndex({ registers }) {
    function fmt(v) { return Number(v || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }); }

    return (
        <>
            <Head title="Caixa" />
            <div className="space-y-4">
                <div className="flex items-center justify-between">
                    <div><h1 className="text-xl font-bold text-[var(--color-text)]">Caixa</h1><p className="text-sm text-[var(--color-text-muted)]">{registers.total} registro(s)</p></div>
                    <Link href="/caixa/abrir" className="px-4 py-2 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition-colors">Abrir Caixa</Link>
                </div>
                <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead><tr className="bg-gray-50 border-b border-[var(--color-border)]">
                                <th className="text-left px-4 py-3 font-semibold text-[var(--color-text)]">Operador</th>
                                <th className="text-center px-4 py-3 font-semibold text-[var(--color-text)]">Status</th>
                                <th className="text-right px-4 py-3 font-semibold text-[var(--color-text)]">Saldo Inicial</th>
                                <th className="text-right px-4 py-3 font-semibold text-[var(--color-text)]">Saldo Final</th>
                                <th className="text-right px-4 py-3 font-semibold text-[var(--color-text)]">Aberto em</th>
                                <th className="text-right px-4 py-3 font-semibold text-[var(--color-text)]">Fechado em</th>
                                <th className="text-right px-4 py-3 font-semibold text-[var(--color-text)]">Ações</th>
                            </tr></thead>
                            <tbody>
                                {registers.data.length === 0 ? (
                                    <tr><td colSpan={7} className="px-4 py-8 text-center text-[var(--color-text-muted)]">Nenhum caixa encontrado.</td></tr>
                                ) : registers.data.map((r) => {
                                    const st = statusLabels[r.status] || statusLabels.closed;
                                    return (
                                        <tr key={r.id} className="border-b border-[var(--color-border)] hover:bg-gray-50 transition-colors">
                                            <td className="px-4 py-3 text-[var(--color-text)]">{r.user?.name || '—'}</td>
                                            <td className="px-4 py-3 text-center"><span className={`inline-block px-2 py-0.5 rounded-full text-xs font-medium ${st.c}`}>{st.l}</span></td>
                                            <td className="px-4 py-3 text-right text-[var(--color-text)]">{fmt(r.opening_balance)}</td>
                                            <td className="px-4 py-3 text-right text-[var(--color-text)]">{r.closing_balance ? fmt(r.closing_balance) : '—'}</td>
                                            <td className="px-4 py-3 text-right text-xs text-[var(--color-text-muted)]">{r.opened_at ? new Date(r.opened_at).toLocaleString('pt-BR') : '—'}</td>
                                            <td className="px-4 py-3 text-right text-xs text-[var(--color-text-muted)]">{r.closed_at ? new Date(r.closed_at).toLocaleString('pt-BR') : '—'}</td>
                                            <td className="px-4 py-3 text-right"><Link href={`/caixa/${r.id}`} className="text-xs text-[var(--color-primary)] hover:underline">Ver</Link></td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    </div>
                    {registers.last_page > 1 && (
                        <div className="flex items-center justify-between px-4 py-3 border-t border-[var(--color-border)]">
                            <span className="text-xs text-[var(--color-text-muted)]">Página {registers.current_page} de {registers.last_page}</span>
                            <div className="flex gap-1">{registers.links.map((link, i) => (<Link key={i} href={link.url || '#'} className={`px-3 py-1 rounded text-xs ${link.active ? 'bg-[var(--color-primary)] text-white' : link.url ? 'text-[var(--color-text-muted)] hover:bg-gray-100' : 'text-gray-300 cursor-not-allowed'}`} dangerouslySetInnerHTML={{ __html: link.label }} />))}</div>
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}
