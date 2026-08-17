import { Link, router, Head } from '@inertiajs/react';
import { useState } from 'react';

const statusLabels = { pending: { l: 'Pendente', c: 'bg-yellow-100 text-yellow-800' }, paid: { l: 'Pago', c: 'bg-green-100 text-green-800' }, overdue: { l: 'Vencido', c: 'bg-red-100 text-red-800' }, cancelled: { l: 'Cancelado', c: 'bg-gray-100 text-gray-800' } };
const typeLabels = { receivable: 'A Receber', payable: 'A Pagar' };

export default function FinancialIndex({ entries, filters }) {
    const [search, setSearch] = useState(filters?.q || '');
    const [type, setType] = useState(filters?.type || '');
    const [status, setStatus] = useState(filters?.status || '');
    function handleSearch(e) { e.preventDefault(); router.get('/financeiro', { q: search, type, status }, { preserveState: true }); }
    function fmt(v) { return Number(v || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }); }

    return (
        <>
            <Head title="Financeiro" />
            <div className="space-y-4">
                <div className="flex items-center justify-between">
                    <div><h1 className="text-xl font-bold text-[var(--color-text)]">Financeiro</h1><p className="text-sm text-[var(--color-text-muted)]">{entries.total} lançamento(s)</p></div>
                    <Link href="/financeiro/novo" className="px-4 py-2 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition-colors">+ Novo Lançamento</Link>
                </div>
                <form onSubmit={handleSearch} className="flex gap-3 flex-wrap">
                    <input type="text" value={search} onChange={(e) => setSearch(e.target.value)} placeholder="Buscar por descrição..." className="flex-1 min-w-[200px] px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]" />
                    <select value={type} onChange={(e) => setType(e.target.value)} className="px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white">
                        <option value="">Tipo</option>
                        <option value="receivable">A Receber</option>
                        <option value="payable">A Pagar</option>
                    </select>
                    <select value={status} onChange={(e) => setStatus(e.target.value)} className="px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white">
                        <option value="">Status</option>
                        <option value="pending">Pendente</option>
                        <option value="paid">Pago</option>
                        <option value="overdue">Vencido</option>
                    </select>
                    <button type="submit" className="px-4 py-2 rounded-lg bg-[var(--color-primary)] text-white text-sm hover:bg-[var(--color-primary-dark)] transition-colors">Buscar</button>
                </form>
                <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead><tr className="bg-gray-50 border-b border-[var(--color-border)]">
                                <th className="text-left px-4 py-3 font-semibold text-[var(--color-text)]">Descrição</th>
                                <th className="text-center px-4 py-3 font-semibold text-[var(--color-text)]">Tipo</th>
                                <th className="text-center px-4 py-3 font-semibold text-[var(--color-text)]">Status</th>
                                <th className="text-right px-4 py-3 font-semibold text-[var(--color-text)]">Valor</th>
                                <th className="text-right px-4 py-3 font-semibold text-[var(--color-text)]">Vencimento</th>
                                <th className="text-right px-4 py-3 font-semibold text-[var(--color-text)]">Ações</th>
                            </tr></thead>
                            <tbody>
                                {entries.data.length === 0 ? (
                                    <tr><td colSpan={6} className="px-4 py-8 text-center text-[var(--color-text-muted)]">Nenhum lançamento encontrado.</td></tr>
                                ) : entries.data.map((e) => {
                                    const st = statusLabels[e.status] || statusLabels.pending;
                                    return (
                                        <tr key={e.id} className="border-b border-[var(--color-border)] hover:bg-gray-50 transition-colors">
                                            <td className="px-4 py-3 text-[var(--color-text)]">{e.description}</td>
                                            <td className="px-4 py-3 text-center"><span className={`inline-block px-2 py-0.5 rounded-full text-xs font-medium ${e.type === 'receivable' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800'}`}>{typeLabels[e.type]}</span></td>
                                            <td className="px-4 py-3 text-center"><span className={`inline-block px-2 py-0.5 rounded-full text-xs font-medium ${st.c}`}>{st.l}</span></td>
                                            <td className={`px-4 py-3 text-right font-medium ${e.type === 'receivable' ? 'text-[var(--color-success)]' : 'text-[var(--color-danger)]'}`}>{fmt(e.amount)}</td>
                                            <td className="px-4 py-3 text-right text-xs text-[var(--color-text-muted)]">{e.due_date ? new Date(e.due_date).toLocaleDateString('pt-BR') : '—'}</td>
                                            <td className="px-4 py-3 text-right"><Link href={`/financeiro/${e.id}`} className="text-xs text-[var(--color-primary)] hover:underline">Ver</Link></td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    </div>
                    {entries.last_page > 1 && (
                        <div className="flex items-center justify-between px-4 py-3 border-t border-[var(--color-border)]">
                            <span className="text-xs text-[var(--color-text-muted)]">Página {entries.current_page} de {entries.last_page}</span>
                            <div className="flex gap-1">{entries.links.map((link, i) => (<Link key={i} href={link.url || '#'} className={`px-3 py-1 rounded text-xs ${link.active ? 'bg-[var(--color-primary)] text-white' : link.url ? 'text-[var(--color-text-muted)] hover:bg-gray-100' : 'text-gray-300 cursor-not-allowed'}`} dangerouslySetInnerHTML={{ __html: link.label }} />))}</div>
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}
