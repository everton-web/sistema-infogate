import { Link, router, Head } from '@inertiajs/react';
import { useState } from 'react';

const statusLabels = { active: { l: 'Ativa', c: 'bg-green-100 text-green-800' }, expired: { l: 'Expirada', c: 'bg-gray-100 text-gray-800' }, claimed: { l: 'Acionada', c: 'bg-yellow-100 text-yellow-800' }, voided: { l: 'Cancelada', c: 'bg-red-100 text-red-800' } };

export default function WarrantiesIndex({ warranties, filters }) {
    const [search, setSearch] = useState(filters?.q || '');
    const [status, setStatus] = useState(filters?.status || '');
    function handleSearch(e) { e.preventDefault(); router.get('/garantias', { q: search, status }, { preserveState: true }); }

    return (
        <>
            <Head title="Garantias" />
            <div className="space-y-4">
                <div className="flex items-center justify-between">
                    <div><h1 className="text-xl font-bold text-[var(--color-text)]">Garantias</h1><p className="text-sm text-[var(--color-text-muted)]">{warranties.total} garantia(s)</p></div>
                    <Link href="/garantias/nova" className="px-4 py-2 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition-colors">+ Nova Garantia</Link>
                </div>
                <form onSubmit={handleSearch} className="flex gap-3">
                    <input type="text" value={search} onChange={(e) => setSearch(e.target.value)} placeholder="Buscar por cliente ou produto..." className="flex-1 px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]" />
                    <select value={status} onChange={(e) => setStatus(e.target.value)} className="px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white">
                        <option value="">Todos</option>
                        <option value="active">Ativas</option>
                        <option value="expired">Expiradas</option>
                        <option value="claimed">Acionadas</option>
                        <option value="voided">Canceladas</option>
                    </select>
                    <button type="submit" className="px-4 py-2 rounded-lg bg-[var(--color-primary)] text-white text-sm hover:bg-[var(--color-primary-dark)] transition-colors">Buscar</button>
                </form>
                <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead><tr className="bg-gray-50 border-b border-[var(--color-border)]">
                                <th className="text-left px-4 py-3 font-semibold text-[var(--color-text)]">Cliente</th>
                                <th className="text-left px-4 py-3 font-semibold text-[var(--color-text)]">Produto</th>
                                <th className="text-center px-4 py-3 font-semibold text-[var(--color-text)]">Status</th>
                                <th className="text-right px-4 py-3 font-semibold text-[var(--color-text)]">Início</th>
                                <th className="text-right px-4 py-3 font-semibold text-[var(--color-text)]">Fim</th>
                                <th className="text-right px-4 py-3 font-semibold text-[var(--color-text)]">Ações</th>
                            </tr></thead>
                            <tbody>
                                {warranties.data.length === 0 ? (
                                    <tr><td colSpan={6} className="px-4 py-8 text-center text-[var(--color-text-muted)]">Nenhuma garantia encontrada.</td></tr>
                                ) : warranties.data.map((w) => {
                                    const st = statusLabels[w.status] || statusLabels.active;
                                    return (
                                        <tr key={w.id} className="border-b border-[var(--color-border)] hover:bg-gray-50 transition-colors">
                                            <td className="px-4 py-3 text-[var(--color-text)]">{w.customer?.name || '—'}</td>
                                            <td className="px-4 py-3 text-[var(--color-text-muted)]">{w.product?.name || w.description || '—'}</td>
                                            <td className="px-4 py-3 text-center"><span className={`inline-block px-2 py-0.5 rounded-full text-xs font-medium ${st.c}`}>{st.l}</span></td>
                                            <td className="px-4 py-3 text-right text-xs text-[var(--color-text-muted)]">{w.start_date ? new Date(w.start_date).toLocaleDateString('pt-BR') : '—'}</td>
                                            <td className="px-4 py-3 text-right text-xs text-[var(--color-text-muted)]">{w.end_date ? new Date(w.end_date).toLocaleDateString('pt-BR') : '—'}</td>
                                            <td className="px-4 py-3 text-right"><Link href={`/garantias/${w.id}`} className="text-xs text-[var(--color-primary)] hover:underline">Ver</Link></td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    </div>
                    {warranties.last_page > 1 && (
                        <div className="flex items-center justify-between px-4 py-3 border-t border-[var(--color-border)]">
                            <span className="text-xs text-[var(--color-text-muted)]">Página {warranties.current_page} de {warranties.last_page}</span>
                            <div className="flex gap-1">{warranties.links.map((link, i) => (<Link key={i} href={link.url || '#'} className={`px-3 py-1 rounded text-xs ${link.active ? 'bg-[var(--color-primary)] text-white' : link.url ? 'text-[var(--color-text-muted)] hover:bg-gray-100' : 'text-gray-300 cursor-not-allowed'}`} dangerouslySetInnerHTML={{ __html: link.label }} />))}</div>
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}
