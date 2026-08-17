import { router, Head } from '@inertiajs/react';
import { useState } from 'react';

export default function ReportsIndex({ salesTotal, purchasesTotal, receivables, payables, topProducts, topCustomers, filters }) {
    const [startDate, setStartDate] = useState(filters?.start_date || '');
    const [endDate, setEndDate] = useState(filters?.end_date || '');
    function handleFilter(e) { e.preventDefault(); router.get('/relatorios', { start_date: startDate, end_date: endDate }, { preserveState: true }); }
    function fmt(v) { return Number(v || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }); }

    const profit = (salesTotal || 0) - (purchasesTotal || 0);

    return (
        <>
            <Head title="Relatórios" />
            <div className="space-y-6">
                <div>
                    <h1 className="text-xl font-bold text-[var(--color-text)]">Relatórios</h1>
                    <p className="text-sm text-[var(--color-text-muted)]">Resumo financeiro do período</p>
                </div>

                <form onSubmit={handleFilter} className="flex items-end gap-3 flex-wrap">
                    <div>
                        <label className="block text-xs text-[var(--color-text-muted)] mb-1">Data Início</label>
                        <input type="date" value={startDate} onChange={(e) => setStartDate(e.target.value)} className="px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white" />
                    </div>
                    <div>
                        <label className="block text-xs text-[var(--color-text-muted)] mb-1">Data Fim</label>
                        <input type="date" value={endDate} onChange={(e) => setEndDate(e.target.value)} className="px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white" />
                    </div>
                    <button type="submit" className="px-4 py-2 rounded-lg bg-[var(--color-primary)] text-white text-sm hover:bg-[var(--color-primary-dark)] transition-colors">Filtrar</button>
                </form>

                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5">
                        <p className="text-xs text-[var(--color-text-muted)]">Vendas</p>
                        <p className="text-xl font-bold text-[var(--color-success)]">{fmt(salesTotal)}</p>
                    </div>
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5">
                        <p className="text-xs text-[var(--color-text-muted)]">Compras</p>
                        <p className="text-xl font-bold text-[var(--color-danger)]">{fmt(purchasesTotal)}</p>
                    </div>
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5">
                        <p className="text-xs text-[var(--color-text-muted)]">A Receber</p>
                        <p className="text-xl font-bold text-blue-600">{fmt(receivables)}</p>
                    </div>
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5">
                        <p className="text-xs text-[var(--color-text-muted)]">A Pagar</p>
                        <p className="text-xl font-bold text-orange-600">{fmt(payables)}</p>
                    </div>
                </div>

                <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5">
                    <p className="text-xs text-[var(--color-text-muted)]">Resultado (Vendas - Compras)</p>
                    <p className={`text-2xl font-bold ${profit >= 0 ? 'text-[var(--color-success)]' : 'text-[var(--color-danger)]'}`}>{fmt(profit)}</p>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5">
                        <h2 className="text-sm font-semibold text-[var(--color-text)] mb-4">Top Produtos Vendidos</h2>
                        {topProducts?.length > 0 ? (
                            <div className="space-y-2">
                                {topProducts.map((p, i) => (
                                    <div key={i} className="flex items-center justify-between text-sm">
                                        <span className="text-[var(--color-text)]">{i + 1}. {p.description}</span>
                                        <span className="text-[var(--color-text-muted)]">{p.total_qty} un. | {fmt(p.total_value)}</span>
                                    </div>
                                ))}
                            </div>
                        ) : <p className="text-sm text-[var(--color-text-muted)]">Sem dados no período.</p>}
                    </div>
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5">
                        <h2 className="text-sm font-semibold text-[var(--color-text)] mb-4">Top Clientes</h2>
                        {topCustomers?.length > 0 ? (
                            <div className="space-y-2">
                                {topCustomers.map((c, i) => (
                                    <div key={i} className="flex items-center justify-between text-sm">
                                        <span className="text-[var(--color-text)]">{i + 1}. {c.customer?.name || 'Consumidor'}</span>
                                        <span className="text-[var(--color-text-muted)]">{c.total_sales} venda(s) | {fmt(c.total_value)}</span>
                                    </div>
                                ))}
                            </div>
                        ) : <p className="text-sm text-[var(--color-text-muted)]">Sem dados no período.</p>}
                    </div>
                </div>
            </div>
        </>
    );
}
