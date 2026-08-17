import { Link, Head } from '@inertiajs/react';

export default function ProductShow({ product }) {
    function formatCurrency(value) {
        return Number(value || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }

    return (
        <>
            <Head title={product.name} />
            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <Link href="/cadastros/produtos" className="text-xs text-[var(--color-primary)] hover:underline">← Voltar</Link>
                        <h1 className="text-xl font-bold text-[var(--color-text)] mt-1">{product.name}</h1>
                        <span className={`inline-block mt-1 px-2 py-0.5 rounded-full text-xs font-medium ${product.type === 'product' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'}`}>
                            {product.type === 'product' ? 'Produto' : 'Serviço'}
                        </span>
                    </div>
                    <Link href={`/cadastros/produtos/${product.id}/editar`} className="px-4 py-2 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition-colors">
                        Editar
                    </Link>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5 space-y-3">
                        <h2 className="text-sm font-semibold text-[var(--color-text)]">Informações</h2>
                        <dl className="grid grid-cols-2 gap-3 text-sm">
                            <div><dt className="text-[var(--color-text-muted)]">SKU</dt><dd className="font-medium text-[var(--color-text)]">{product.sku || '—'}</dd></div>
                            <div><dt className="text-[var(--color-text-muted)]">Código de Barras</dt><dd className="font-medium text-[var(--color-text)]">{product.barcode || '—'}</dd></div>
                            <div><dt className="text-[var(--color-text-muted)]">Unidade</dt><dd className="font-medium text-[var(--color-text)]">{product.unit}</dd></div>
                            <div><dt className="text-[var(--color-text-muted)]">Categoria</dt><dd className="font-medium text-[var(--color-text)]">{product.category || '—'}</dd></div>
                            <div><dt className="text-[var(--color-text-muted)]">Marca</dt><dd className="font-medium text-[var(--color-text)]">{product.brand || '—'}</dd></div>
                            <div><dt className="text-[var(--color-text-muted)]">NCM</dt><dd className="font-medium text-[var(--color-text)]">{product.ncm || '—'}</dd></div>
                            <div><dt className="text-[var(--color-text-muted)]">Status</dt><dd><span className={`inline-block px-2 py-0.5 rounded-full text-xs font-medium ${product.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>{product.status === 'active' ? 'Ativo' : 'Inativo'}</span></dd></div>
                        </dl>
                    </div>

                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5 space-y-3">
                        <h2 className="text-sm font-semibold text-[var(--color-text)]">Preços e Estoque</h2>
                        <dl className="grid grid-cols-2 gap-3 text-sm">
                            <div><dt className="text-[var(--color-text-muted)]">Preço de Custo</dt><dd className="font-medium text-[var(--color-text)]">{formatCurrency(product.cost_price)}</dd></div>
                            <div><dt className="text-[var(--color-text-muted)]">Preço de Venda</dt><dd className="font-medium text-[var(--color-text)]">{formatCurrency(product.sale_price)}</dd></div>
                            {product.type === 'product' && (
                                <>
                                    <div><dt className="text-[var(--color-text-muted)]">Estoque Atual</dt><dd className="font-medium text-[var(--color-text)]">{product.stock_quantity}</dd></div>
                                    <div><dt className="text-[var(--color-text-muted)]">Estoque Mínimo</dt><dd className="font-medium text-[var(--color-text)]">{product.stock_minimum}</dd></div>
                                </>
                            )}
                        </dl>
                    </div>
                </div>

                {product.description && (
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5">
                        <h2 className="text-sm font-semibold text-[var(--color-text)] mb-2">Descrição</h2>
                        <p className="text-sm text-[var(--color-text-muted)] whitespace-pre-wrap">{product.description}</p>
                    </div>
                )}
            </div>
        </>
    );
}
