import { Link, router, Head } from '@inertiajs/react';
import { useState } from 'react';

export default function StockIndex({ products, filters }) {
    const [search, setSearch] = useState(filters?.q || '');

    function handleSearch(e) {
        e.preventDefault();
        router.get('/estoque', { q: search }, { preserveState: true });
    }

    return (
        <>
            <Head title="Estoque" />
            <div className="space-y-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-xl font-bold text-[var(--color-text)]">Estoque</h1>
                        <p className="text-sm text-[var(--color-text-muted)]">{products.total} produto(s)</p>
                    </div>
                </div>

                <form onSubmit={handleSearch} className="flex gap-3">
                    <input type="text" value={search} onChange={(e) => setSearch(e.target.value)} placeholder="Buscar por nome ou SKU..." className="flex-1 px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]" />
                    <button type="submit" className="px-4 py-2 rounded-lg bg-[var(--color-primary)] text-white text-sm hover:bg-[var(--color-primary-dark)] transition-colors">Buscar</button>
                </form>

                <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="bg-gray-50 border-b border-[var(--color-border)]">
                                    <th className="text-left px-4 py-3 font-semibold text-[var(--color-text)]">Produto</th>
                                    <th className="text-left px-4 py-3 font-semibold text-[var(--color-text)]">SKU</th>
                                    <th className="text-right px-4 py-3 font-semibold text-[var(--color-text)]">Estoque Atual</th>
                                    <th className="text-right px-4 py-3 font-semibold text-[var(--color-text)]">Estoque Mínimo</th>
                                    <th className="text-center px-4 py-3 font-semibold text-[var(--color-text)]">Situação</th>
                                    <th className="text-right px-4 py-3 font-semibold text-[var(--color-text)]">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                {products.data.length === 0 ? (
                                    <tr><td colSpan={6} className="px-4 py-8 text-center text-[var(--color-text-muted)]">Nenhum produto encontrado.</td></tr>
                                ) : (
                                    products.data.map((product) => {
                                        const isLow = Number(product.stock_quantity) <= Number(product.stock_minimum);
                                        return (
                                            <tr key={product.id} className="border-b border-[var(--color-border)] hover:bg-gray-50 transition-colors">
                                                <td className="px-4 py-3 font-medium text-[var(--color-text)]">{product.name}</td>
                                                <td className="px-4 py-3 text-[var(--color-text-muted)]">{product.sku || '—'}</td>
                                                <td className={`px-4 py-3 text-right font-medium ${isLow ? 'text-[var(--color-danger)]' : 'text-[var(--color-text)]'}`}>{product.stock_quantity}</td>
                                                <td className="px-4 py-3 text-right text-[var(--color-text-muted)]">{product.stock_minimum}</td>
                                                <td className="px-4 py-3 text-center">
                                                    <span className={`inline-block px-2 py-0.5 rounded-full text-xs font-medium ${isLow ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'}`}>
                                                        {isLow ? 'Baixo' : 'OK'}
                                                    </span>
                                                </td>
                                                <td className="px-4 py-3 text-right">
                                                    <Link href={`/estoque/${product.id}/movimentacoes`} className="text-xs text-[var(--color-primary)] hover:underline">Movimentações</Link>
                                                </td>
                                            </tr>
                                        );
                                    })
                                )}
                            </tbody>
                        </table>
                    </div>
                    {products.last_page > 1 && (
                        <div className="flex items-center justify-between px-4 py-3 border-t border-[var(--color-border)]">
                            <span className="text-xs text-[var(--color-text-muted)]">Página {products.current_page} de {products.last_page}</span>
                            <div className="flex gap-1">
                                {products.links.map((link, i) => (
                                    <Link key={i} href={link.url || '#'} className={`px-3 py-1 rounded text-xs ${link.active ? 'bg-[var(--color-primary)] text-white' : link.url ? 'text-[var(--color-text-muted)] hover:bg-gray-100' : 'text-gray-300 cursor-not-allowed'}`} dangerouslySetInnerHTML={{ __html: link.label }} />
                                ))}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}
