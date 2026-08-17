import { Link, Head } from '@inertiajs/react';

const statusLabels = {
    draft: { label: 'Rascunho', color: 'bg-gray-100 text-gray-800' },
    open: { label: 'Aberta', color: 'bg-blue-100 text-blue-800' },
    in_progress: { label: 'Em Andamento', color: 'bg-yellow-100 text-yellow-800' },
    completed: { label: 'Concluída', color: 'bg-green-100 text-green-800' },
    cancelled: { label: 'Cancelada', color: 'bg-red-100 text-red-800' },
};

export default function ServiceOrderShow({ order }) {
    const st = statusLabels[order.status] || statusLabels.draft;

    function formatCurrency(value) {
        return Number(value || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }

    return (
        <>
            <Head title={`OS #${order.number}`} />
            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <Link href="/ordens-servico" className="text-xs text-[var(--color-primary)] hover:underline">← Voltar</Link>
                        <div className="flex items-center gap-3 mt-1">
                            <h1 className="text-xl font-bold text-[var(--color-text)]">OS #{order.number}</h1>
                            <span className={`inline-block px-2 py-0.5 rounded-full text-xs font-medium ${st.color}`}>{st.label}</span>
                        </div>
                    </div>
                    <span className="text-sm text-[var(--color-text-muted)]">
                        {order.opened_at ? new Date(order.opened_at).toLocaleDateString('pt-BR') : ''}
                    </span>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5 space-y-3">
                        <h2 className="text-sm font-semibold text-[var(--color-text)]">Cliente</h2>
                        <dl className="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <dt className="text-[var(--color-text-muted)]">Nome</dt>
                                <dd className="font-medium text-[var(--color-text)]">
                                    {order.customer ? (
                                        <Link href={`/cadastros/clientes/${order.customer.id}`} className="text-[var(--color-primary)] hover:underline">{order.customer.name}</Link>
                                    ) : '—'}
                                </dd>
                            </div>
                            <div>
                                <dt className="text-[var(--color-text-muted)]">Veículo</dt>
                                <dd className="font-medium text-[var(--color-text)]">
                                    {order.vehicle ? `${order.vehicle.plate} - ${order.vehicle.brand?.name || ''} ${order.vehicle.model?.name || ''}` : '—'}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5 space-y-3">
                        <h2 className="text-sm font-semibold text-[var(--color-text)]">Detalhes</h2>
                        <dl className="space-y-2 text-sm">
                            {order.complaint && (
                                <div>
                                    <dt className="text-[var(--color-text-muted)]">Reclamação</dt>
                                    <dd className="text-[var(--color-text)] whitespace-pre-wrap">{order.complaint}</dd>
                                </div>
                            )}
                            {order.diagnosis && (
                                <div>
                                    <dt className="text-[var(--color-text-muted)]">Diagnóstico</dt>
                                    <dd className="text-[var(--color-text)] whitespace-pre-wrap">{order.diagnosis}</dd>
                                </div>
                            )}
                            {order.internal_notes && (
                                <div>
                                    <dt className="text-[var(--color-text-muted)]">Observações Internas</dt>
                                    <dd className="text-[var(--color-text)] whitespace-pre-wrap">{order.internal_notes}</dd>
                                </div>
                            )}
                        </dl>
                    </div>
                </div>

                <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5">
                    <h2 className="text-sm font-semibold text-[var(--color-text)] mb-4">Itens ({order.items?.length || 0})</h2>

                    {order.items?.length > 0 ? (
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm">
                                <thead>
                                    <tr className="border-b border-[var(--color-border)]">
                                        <th className="text-left py-2 font-semibold text-[var(--color-text)]">Tipo</th>
                                        <th className="text-left py-2 font-semibold text-[var(--color-text)]">Descrição</th>
                                        <th className="text-right py-2 font-semibold text-[var(--color-text)]">Qtd</th>
                                        <th className="text-right py-2 font-semibold text-[var(--color-text)]">Valor Unit.</th>
                                        <th className="text-right py-2 font-semibold text-[var(--color-text)]">Desc.</th>
                                        <th className="text-right py-2 font-semibold text-[var(--color-text)]">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {order.items.map((item) => (
                                        <tr key={item.id} className="border-b border-[var(--color-border)] last:border-0">
                                            <td className="py-2">
                                                <span className={`inline-block px-2 py-0.5 rounded-full text-xs font-medium ${item.type === 'product' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'}`}>
                                                    {item.type === 'product' ? 'Produto' : 'Serviço'}
                                                </span>
                                            </td>
                                            <td className="py-2 text-[var(--color-text)]">{item.description}</td>
                                            <td className="py-2 text-right text-[var(--color-text-muted)]">{item.quantity}</td>
                                            <td className="py-2 text-right text-[var(--color-text-muted)]">{formatCurrency(item.unit_price)}</td>
                                            <td className="py-2 text-right text-[var(--color-text-muted)]">{formatCurrency(item.discount)}</td>
                                            <td className="py-2 text-right font-medium text-[var(--color-text)]">{formatCurrency(item.total)}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    ) : (
                        <p className="text-sm text-[var(--color-text-muted)]">Nenhum item nesta OS.</p>
                    )}

                    <div className="flex justify-end pt-4 mt-4 border-t border-[var(--color-border)]">
                        <div className="text-right space-y-1">
                            <p className="text-sm text-[var(--color-text-muted)]">Subtotal: {formatCurrency(order.items?.reduce((s, i) => s + Number(i.total), 0) || 0)}</p>
                            {Number(order.discount) > 0 && (
                                <p className="text-sm text-[var(--color-danger)]">Desconto: -{formatCurrency(order.discount)}</p>
                            )}
                            <p className="text-xl font-bold text-[var(--color-text)]">Total: {formatCurrency(order.total)}</p>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
