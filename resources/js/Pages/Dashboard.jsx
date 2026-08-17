
function StatCard({ title, value, subtitle, color = 'primary' }) {
    const colorMap = {
        primary: 'bg-[var(--color-primary)]',
        success: 'bg-[var(--color-success)]',
        warning: 'bg-[var(--color-warning)]',
        danger: 'bg-[var(--color-danger)]',
    };

    return (
        <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5">
            <div className="flex items-center justify-between">
                <div>
                    <p className="text-sm text-[var(--color-text-muted)]">{title}</p>
                    <p className="text-2xl font-bold text-[var(--color-text)] mt-1">{value}</p>
                    {subtitle && (
                        <p className="text-xs text-[var(--color-text-muted)] mt-1">{subtitle}</p>
                    )}
                </div>
                <div className={`w-10 h-10 rounded-lg ${colorMap[color]} opacity-20`} />
            </div>
        </div>
    );
}

export default function Dashboard({ totalCustomers, activeCustomers, totalVehicles, recentCustomers, recentVehicles }) {
    return (
        <>
            <div className="space-y-6">
                <div>
                    <h1 className="text-xl font-bold text-[var(--color-text)]">Dashboard</h1>
                    <p className="text-sm text-[var(--color-text-muted)]">Visão geral do sistema</p>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <StatCard
                        title="Total de Clientes"
                        value={totalCustomers ?? 0}
                        color="primary"
                    />
                    <StatCard
                        title="Clientes Ativos"
                        value={activeCustomers ?? 0}
                        color="success"
                    />
                    <StatCard
                        title="Veículos Cadastrados"
                        value={totalVehicles ?? 0}
                        color="warning"
                    />
                    <StatCard
                        title="Ordens de Serviço"
                        value={0}
                        subtitle="Em breve"
                        color="danger"
                    />
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5">
                        <h2 className="text-sm font-semibold text-[var(--color-text)] mb-4">
                            Últimos Clientes
                        </h2>
                        {recentCustomers?.length > 0 ? (
                            <ul className="space-y-3">
                                {recentCustomers.map((customer) => (
                                    <li
                                        key={customer.id}
                                        className="flex items-center justify-between py-2 border-b border-[var(--color-border)] last:border-0"
                                    >
                                        <div>
                                            <p className="text-sm font-medium text-[var(--color-text)]">
                                                {customer.name}
                                            </p>
                                            <p className="text-xs text-[var(--color-text-muted)]">
                                                {customer.document || 'Sem documento'}
                                            </p>
                                        </div>
                                        <span className="text-xs text-[var(--color-text-muted)]">
                                            {customer.phone || '—'}
                                        </span>
                                    </li>
                                ))}
                            </ul>
                        ) : (
                            <p className="text-sm text-[var(--color-text-muted)]">
                                Nenhum cliente cadastrado ainda.
                            </p>
                        )}
                    </div>

                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5">
                        <h2 className="text-sm font-semibold text-[var(--color-text)] mb-4">
                            Últimos Veículos
                        </h2>
                        {recentVehicles?.length > 0 ? (
                            <ul className="space-y-3">
                                {recentVehicles.map((vehicle) => (
                                    <li
                                        key={vehicle.id}
                                        className="flex items-center justify-between py-2 border-b border-[var(--color-border)] last:border-0"
                                    >
                                        <div>
                                            <p className="text-sm font-medium text-[var(--color-text)]">
                                                {vehicle.plate}
                                            </p>
                                            <p className="text-xs text-[var(--color-text-muted)]">
                                                {vehicle.brand_name} {vehicle.model_name}
                                            </p>
                                        </div>
                                        <span className="text-xs text-[var(--color-text-muted)]">
                                            {vehicle.year || '—'}
                                        </span>
                                    </li>
                                ))}
                            </ul>
                        ) : (
                            <p className="text-sm text-[var(--color-text-muted)]">
                                Nenhum veículo cadastrado ainda.
                            </p>
                        )}
                    </div>
                </div>
            </div>
        </>
    );
}
