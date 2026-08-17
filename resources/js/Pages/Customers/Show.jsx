import { Link, Head } from '@inertiajs/react';

export default function CustomerShow({ customer }) {
    return (
        <>
            <Head title={customer.name} />
            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <Link
                            href="/cadastros/clientes"
                            className="text-xs text-[var(--color-primary)] hover:underline"
                        >
                            ← Voltar para clientes
                        </Link>
                        <h1 className="text-xl font-bold text-[var(--color-text)] mt-1">{customer.name}</h1>
                        {customer.trade_name && (
                            <p className="text-sm text-[var(--color-text-muted)]">{customer.trade_name}</p>
                        )}
                    </div>
                    <Link
                        href={`/cadastros/clientes/${customer.id}/editar`}
                        className="px-4 py-2 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition-colors"
                    >
                        Editar
                    </Link>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5 space-y-3">
                        <h2 className="text-sm font-semibold text-[var(--color-text)]">Dados Pessoais</h2>
                        <dl className="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <dt className="text-[var(--color-text-muted)]">Tipo</dt>
                                <dd className="font-medium text-[var(--color-text)]">{customer.type === 'pf' ? 'Pessoa Física' : 'Pessoa Jurídica'}</dd>
                            </div>
                            <div>
                                <dt className="text-[var(--color-text-muted)]">Documento</dt>
                                <dd className="font-medium text-[var(--color-text)]">{customer.document || '—'}</dd>
                            </div>
                            <div>
                                <dt className="text-[var(--color-text-muted)]">Telefone</dt>
                                <dd className="font-medium text-[var(--color-text)]">{customer.phone || '—'}</dd>
                            </div>
                            <div>
                                <dt className="text-[var(--color-text-muted)]">WhatsApp</dt>
                                <dd className="font-medium text-[var(--color-text)]">{customer.whatsapp || '—'}</dd>
                            </div>
                            <div className="col-span-2">
                                <dt className="text-[var(--color-text-muted)]">E-mail</dt>
                                <dd className="font-medium text-[var(--color-text)]">{customer.email || '—'}</dd>
                            </div>
                            <div>
                                <dt className="text-[var(--color-text-muted)]">Status</dt>
                                <dd>
                                    <span className={`inline-block px-2 py-0.5 rounded-full text-xs font-medium ${
                                        customer.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                    }`}>
                                        {customer.status === 'active' ? 'Ativo' : 'Inativo'}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5 space-y-3">
                        <h2 className="text-sm font-semibold text-[var(--color-text)]">Endereço</h2>
                        <dl className="grid grid-cols-2 gap-3 text-sm">
                            <div className="col-span-2">
                                <dt className="text-[var(--color-text-muted)]">Logradouro</dt>
                                <dd className="font-medium text-[var(--color-text)]">
                                    {customer.street ? `${customer.street}, ${customer.number || 'S/N'}` : '—'}
                                </dd>
                            </div>
                            <div>
                                <dt className="text-[var(--color-text-muted)]">Bairro</dt>
                                <dd className="font-medium text-[var(--color-text)]">{customer.neighborhood || '—'}</dd>
                            </div>
                            <div>
                                <dt className="text-[var(--color-text-muted)]">Cidade/UF</dt>
                                <dd className="font-medium text-[var(--color-text)]">
                                    {customer.city ? `${customer.city}/${customer.state}` : '—'}
                                </dd>
                            </div>
                            <div>
                                <dt className="text-[var(--color-text-muted)]">CEP</dt>
                                <dd className="font-medium text-[var(--color-text)]">{customer.postal_code || '—'}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {customer.notes && (
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5">
                        <h2 className="text-sm font-semibold text-[var(--color-text)] mb-2">Observações</h2>
                        <p className="text-sm text-[var(--color-text-muted)] whitespace-pre-wrap">{customer.notes}</p>
                    </div>
                )}

                <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5">
                    <h2 className="text-sm font-semibold text-[var(--color-text)] mb-4">
                        Veículos ({customer.vehicles?.length || 0})
                    </h2>
                    {customer.vehicles?.length > 0 ? (
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm">
                                <thead>
                                    <tr className="border-b border-[var(--color-border)]">
                                        <th className="text-left py-2 font-semibold text-[var(--color-text)]">Placa</th>
                                        <th className="text-left py-2 font-semibold text-[var(--color-text)]">Marca/Modelo</th>
                                        <th className="text-left py-2 font-semibold text-[var(--color-text)]">Cor</th>
                                        <th className="text-left py-2 font-semibold text-[var(--color-text)]">Ano</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {customer.vehicles.map((vehicle) => (
                                        <tr key={vehicle.id} className="border-b border-[var(--color-border)] last:border-0">
                                            <td className="py-2 font-medium text-[var(--color-text)]">{vehicle.plate}</td>
                                            <td className="py-2 text-[var(--color-text-muted)]">
                                                {vehicle.brand?.name} {vehicle.model?.name}
                                            </td>
                                            <td className="py-2 text-[var(--color-text-muted)]">{vehicle.color || '—'}</td>
                                            <td className="py-2 text-[var(--color-text-muted)]">{vehicle.year_model || '—'}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    ) : (
                        <p className="text-sm text-[var(--color-text-muted)]">Nenhum veículo vinculado.</p>
                    )}
                </div>
            </div>
        </>
    );
}
