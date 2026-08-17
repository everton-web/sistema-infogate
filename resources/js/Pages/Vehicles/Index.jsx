import { Link, Head } from '@inertiajs/react';

export default function VehiclesIndex({ vehicles }) {
    return (
        <>
            <Head title="Veículos" />
            <div className="space-y-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-xl font-bold text-[var(--color-text)]">Veículos</h1>
                        <p className="text-sm text-[var(--color-text-muted)]">
                            {vehicles.total} veículo(s) cadastrado(s)
                        </p>
                    </div>
                    <Link
                        href="/cadastros/veiculos/novo"
                        className="px-4 py-2 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition-colors"
                    >
                        + Novo Veículo
                    </Link>
                </div>

                <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="bg-gray-50 border-b border-[var(--color-border)]">
                                    <th className="text-left px-4 py-3 font-semibold text-[var(--color-text)]">Placa</th>
                                    <th className="text-left px-4 py-3 font-semibold text-[var(--color-text)]">Marca / Modelo</th>
                                    <th className="text-left px-4 py-3 font-semibold text-[var(--color-text)]">Cliente</th>
                                    <th className="text-left px-4 py-3 font-semibold text-[var(--color-text)]">Cor</th>
                                    <th className="text-left px-4 py-3 font-semibold text-[var(--color-text)]">Ano</th>
                                </tr>
                            </thead>
                            <tbody>
                                {vehicles.data.length === 0 ? (
                                    <tr>
                                        <td colSpan={5} className="px-4 py-8 text-center text-[var(--color-text-muted)]">
                                            Nenhum veículo cadastrado.
                                        </td>
                                    </tr>
                                ) : (
                                    vehicles.data.map((vehicle) => (
                                        <tr key={vehicle.id} className="border-b border-[var(--color-border)] hover:bg-gray-50 transition-colors">
                                            <td className="px-4 py-3 font-medium text-[var(--color-text)]">
                                                {vehicle.plate}
                                            </td>
                                            <td className="px-4 py-3 text-[var(--color-text-muted)]">
                                                {vehicle.brand?.name} {vehicle.model?.name}
                                            </td>
                                            <td className="px-4 py-3">
                                                {vehicle.customer ? (
                                                    <Link
                                                        href={`/cadastros/clientes/${vehicle.customer.id}`}
                                                        className="text-[var(--color-primary)] hover:underline"
                                                    >
                                                        {vehicle.customer.name}
                                                    </Link>
                                                ) : '—'}
                                            </td>
                                            <td className="px-4 py-3 text-[var(--color-text-muted)]">
                                                {vehicle.color || '—'}
                                            </td>
                                            <td className="px-4 py-3 text-[var(--color-text-muted)]">
                                                {vehicle.year_model || '—'}
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>

                    {vehicles.last_page > 1 && (
                        <div className="flex items-center justify-between px-4 py-3 border-t border-[var(--color-border)]">
                            <span className="text-xs text-[var(--color-text-muted)]">
                                Página {vehicles.current_page} de {vehicles.last_page}
                            </span>
                            <div className="flex gap-1">
                                {vehicles.links.map((link, i) => (
                                    <Link
                                        key={i}
                                        href={link.url || '#'}
                                        className={`px-3 py-1 rounded text-xs ${
                                            link.active
                                                ? 'bg-[var(--color-primary)] text-white'
                                                : link.url
                                                ? 'text-[var(--color-text-muted)] hover:bg-gray-100'
                                                : 'text-gray-300 cursor-not-allowed'
                                        }`}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                    />
                                ))}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}
