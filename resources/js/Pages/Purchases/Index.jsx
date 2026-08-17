import { Link, router, Head } from '@inertiajs/react';
import { useState } from 'react';

const statusLabels = { pending: { l: 'Pendente', c: 'bg-yellow-100 text-yellow-800' }, received: { l: 'Recebida', c: 'bg-green-100 text-green-800' }, cancelled: { l: 'Cancelada', c: 'bg-red-100 text-red-800' } };

export default function PurchasesIndex({ purchases, filters }) {
    const [search, setSearch] = useState(filters?.q || '');
    function handleSearch(e) { e.preventDefault(); router.get('/compras', { q: search }, { preserveState: true }); }
    function fmt(v) { return Number(v || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }); }

    return (
        <>
            <Head title="Compras" />
            <div className="space-y-4">
                <div className="flex items-center justify-between">
                    <div><h1 className="text-xl font-bold text-[var(--color-text)]">Compras</h1><p className="text-sm text-[var(--color-text-muted)]">{purchases.total} compra(s)</p></div>
                    <Link href="/compras/nova" className="px-4 py-2 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition-colors">+ Nova Compra</Link>
                </div>
                <form onSubmit={handleSearch} className="flex gap-3">
                    <input type="text" value={search} onChange={(e) => setSearch(e.target.value)} placeholder="Buscar por número ou fornecedor..." className="flex-1 px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]" />
                    <button type="submit" className="px-4 py-2 rounded-lg bg-[var(--color-primary)] text-white text-sm hover:bg-[var(--color-primary-dark)] transition-colors">Buscar</button>
                </form>
                <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead><tr className="bg-gray-50 border-b border-[var(--color-border)]">
                                <th className="text-left px-4 py-3 font-semibold text-[var(--color-text)]">#</th>
                                <th className="text-left px-4 py-3 font-semibold text-[var(--color-text)]">Fornecedor</th>
                                <th className="text-center px-4 py-3 font-semibold text-[var(--color-text)]">Status</th>
                                <th className="text-right px-4 py-3 font-semibold text-[var(--color-text)]">Total</th>
                                <th className="text-right px-4 py-3 font-semibold text-[var(--color-text)]">Data</th>
                            </tr></thead>
                            <tbody>
                                {purchases.data.length === 0 ? (
                                    <tr><td colSpan={5} className="px-4 py-8 text-center text-[var(--color-text-muted)]">Nenhuma compra encontrada.</td></tr>
                                ) : purchases.data.map((p) => {
                                    const st = statusLabels[p.status] || statusLabels.pending;
                                    return (
                                        <tr key={p.id} className="border-b border-[var(--color-border)] hover:bg-gray-50 transition-colors">
                                            <td className="px-4 py-3"><Link href={`/compras/${p.id}`} className="font-medium text-[var(--color-primary)] hover:underline">#{p.number}</Link></td>
                                            <td className="px-4 py-3 text-[var(--color-text)]">{p.supplier?.name || '—'}</td>
                                            <td className="px-4 py-3 text-center"><span className={`inline-block px-2 py-0.5 rounded-full text-xs font-medium ${st.c}`}>{st.l}</span></td>
                                            <td className="px-4 py-3 text-right text-[var(--color-text)]">{fmt(p.total)}</td>
                                            <td className="px-4 py-3 text-right text-xs text-[var(--color-text-muted)]">{p.created_at ? new Date(p.created_at).toLocaleDateString('pt-BR') : '—'}</td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    </div>
                    {purchases.last_page > 1 && (
                        <div className="flex items-center justify-between px-4 py-3 border-t border-[var(--color-border)]">
                            <span className="text-xs text-[var(--color-text-muted)]">Página {purchases.current_page} de {purchases.last_page}</span>
                            <div className="flex gap-1">{purchases.links.map((link, i) => (<Link key={i} href={link.url || '#'} className={`px-3 py-1 rounded text-xs ${link.active ? 'bg-[var(--color-primary)] text-white' : link.url ? 'text-[var(--color-text-muted)] hover:bg-gray-100' : 'text-gray-300 cursor-not-allowed'}`} dangerouslySetInnerHTML={{ __html: link.label }} />))}</div>
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}
