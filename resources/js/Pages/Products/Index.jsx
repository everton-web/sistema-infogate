import { Link, router, Head } from '@inertiajs/react';
import { useState } from 'react';

export default function ProductsIndex({ products, filters }) {
    const [search, setSearch] = useState(filters.q || '');
    const [type, setType] = useState(filters.type || '');

    function handleSearch(e) {
        e.preventDefault();
        router.get('/cadastros/produtos', { q: search, type }, { preserveState: true });
    }

    function formatCurrency(value) {
        return Number(value || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }

    return (
        <>
            <Head title="Produtos e Serviços" />
            <div className="space-y-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-xl font-bold text-[var(--color-text)]">Produtos / Serviços</h1>
                        <p className="text-sm text-[var(--color-text-muted)]">{products.total} item(ns) cadastrado(s)</p>
                    </div>
                    <Link
                        href="/cadastros/produtos/novo"
                        className="px-4 py-2 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition-colors"
                    >
                        + Novo
                    </Link>
                </div>

                <form onSubmit={handleSearch} className="flex gap-3">
                    <input
                        type="text"
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        placeholder="Buscar por nome, SKU, código de barras..."
                        className="flex-1 px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
                    />
                    <select
                        value={type}
                        onChange={(e) => setType(e.target.value)}
                        className="px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white"
                    >
                        <option value="">Todos</option>
                        <option value="product">Produtos</option>
                        <option value="service">Serviços</option>
                    </select>
                    <button type="submit" className="px-4 py-2 rounded-lg bg-[var(--color-primary)] text-white text-sm hover:bg-[var(--color-primary-dark)] transition-colors">
                        Buscar
                    </button>
                </form>

                <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="bg-gray-50 border-b border-[var(--color-border)]">
                                    <th className="text-left px-4 py-3 font-semibold text-[var(--color-text)]">Nome</th>
                                    <th className="text-left px-4 py-3 font-semibold text-[var(--color-text)]">Tipo</th>
                                    <th className="text-left px-4 py-3 font-semibold text-[var(--color-text)]">SKU</th>
                                    <th className="text-right px-4 py-3 font-semibold text-[var(--color-text)]">Preço Venda</th>
                                    <th className="text-right px-4 py-3 font-semibold text-[var(--color-text)]">Estoque</th>
                                    <th className="text-center px-4 py-3 font-semibold text-[var(--color-text)]">Status</th>
                                    <th className="text-right px-4 py-3 font-semibold text-[var(--color-text)]">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                {products.data.length === 0 ? (
                                    <tr>
                                        <td colSpan={7} className="px-4 py-8 text-center text-[var(--color-text-muted)]">Nenhum item encontrado.</td>
                                    </tr>
                                ) : (
                                    products.data.map((product) => (
                                        <tr key={product.id} className="border-b border-[var(--color-border)] hover:bg-gray-50 transition-colors">
                                            <td className="px-4 py-3">
                                                <Link href={`/cadastros/produtos/${product.id}`} className="font-medium text-[var(--color-primary)] hover:underline">
                                                    {product.name}
                                                </Link>
                                                {product.category && <p className="text-xs text-[var(--color-text-muted)]">{product.category}</p>}
                                            </td>
                                            <td className="px-4 py-3">
                                                <span className={`inline-block px-2 py-0.5 rounded-full text-xs font-medium ${
                                                    product.type === 'product' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'
                                                }`}>
                                                    {product.type === 'product' ? 'Produto' : 'Serviço'}
                                                </span>
                                            </td>
                                            <td className="px-4 py-3 text-[var(--color-text-muted)]">{product.sku || '—'}</td>
                                            <td className="px-4 py-3 text-right text-[var(--color-text)]">{formatCurrency(product.sale_price)}</td>
                                            <td className="px-4 py-3 text-right text-[var(--color-text-muted)]">
                                                {product.type === 'product' ? product.stock_quantity : '—'}
                                            </td>
                                            <td className="px-4 py-3 text-center">
                                                <span className={`inline-block px-2 py-0.5 rounded-full text-xs font-medium ${
                                                    product.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                                }`}>
                                                    {product.status === 'active' ? 'Ativo' : 'Inativo'}
                                                </span>
                                            </td>
                                            <td className="px-4 py-3 text-right">
                                                <Link href={`/cadastros/produtos/${product.id}/editar`} className="text-xs text-[var(--color-primary)] hover:underline">
                                                    Editar
                                                </Link>
                                            </td>
                                        </tr>
                                    ))
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
