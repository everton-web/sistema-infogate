import { Link, Head } from '@inertiajs/react';

const statusLabels = { open: { l: 'Aberta', c: 'bg-blue-100 text-blue-800' }, completed: { l: 'Finalizada', c: 'bg-green-100 text-green-800' }, cancelled: { l: 'Cancelada', c: 'bg-red-100 text-red-800' } };
const paymentLabels = { cash: 'Dinheiro', credit_card: 'Crédito', debit_card: 'Débito', pix: 'PIX', boleto: 'Boleto', other: 'Outro' };

export default function SaleShow({ sale }) {
    const st = statusLabels[sale.status] || statusLabels.open;
    function fmt(v) { return Number(v || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }); }

    return (
        <>
            <Head title={`Venda #${sale.number}`} />
            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <Link href="/vendas" className="text-xs text-[var(--color-primary)] hover:underline">← Voltar</Link>
                        <div className="flex items-center gap-3 mt-1">
                            <h1 className="text-xl font-bold text-[var(--color-text)]">Venda #{sale.number}</h1>
                            <span className={`inline-block px-2 py-0.5 rounded-full text-xs font-medium ${st.c}`}>{st.l}</span>
                        </div>
                    </div>
                    <span className="text-sm text-[var(--color-text-muted)]">{sale.created_at ? new Date(sale.created_at).toLocaleDateString('pt-BR') : ''}</span>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5">
                        <h2 className="text-sm font-semibold text-[var(--color-text)] mb-2">Cliente</h2>
                        <p className="text-sm text-[var(--color-text)]">{sale.customer ? <Link href={`/cadastros/clientes/${sale.customer.id}`} className="text-[var(--color-primary)] hover:underline">{sale.customer.name}</Link> : 'Consumidor Final'}</p>
                    </div>
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5">
                        <h2 className="text-sm font-semibold text-[var(--color-text)] mb-2">Pagamento</h2>
                        <p className="text-sm text-[var(--color-text)]">{paymentLabels[sale.payment_method] || '—'}</p>
                    </div>
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5">
                        <h2 className="text-sm font-semibold text-[var(--color-text)] mb-2">Total</h2>
                        <p className="text-xl font-bold text-[var(--color-text)]">{fmt(sale.total)}</p>
                    </div>
                </div>

                <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5">
                    <h2 className="text-sm font-semibold text-[var(--color-text)] mb-4">Itens ({sale.items?.length || 0})</h2>
                    {sale.items?.length > 0 ? (
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm">
                                <thead><tr className="border-b border-[var(--color-border)]">
                                    <th className="text-left py-2 font-semibold text-[var(--color-text)]">Descrição</th>
                                    <th className="text-right py-2 font-semibold text-[var(--color-text)]">Qtd</th>
                                    <th className="text-right py-2 font-semibold text-[var(--color-text)]">Valor Unit.</th>
                                    <th className="text-right py-2 font-semibold text-[var(--color-text)]">Total</th>
                                </tr></thead>
                                <tbody>
                                    {sale.items.map((item) => (
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
                        <div className="text-right space-y-1">
                            {Number(sale.discount) > 0 && <p className="text-sm text-[var(--color-danger)]">Desconto: -{fmt(sale.discount)}</p>}
                            <p className="text-xl font-bold text-[var(--color-text)]">Total: {fmt(sale.total)}</p>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
