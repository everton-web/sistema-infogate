import { Link, router, Head } from '@inertiajs/react';
import { useState } from 'react';

export default function CustomersIndex({ customers, filters }) {
    const [search, setSearch] = useState(filters.q || '');
    const [status, setStatus] = useState(filters.status || '');

    function handleSearch(e) {
        e.preventDefault();
        router.get('/cadastros/clientes', { q: search, status }, { preserveState: true });
    }

    return (
        <>
            <Head title="Clientes" />
            <div className="space-y-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-xl font-bold text-[var(--color-text)]">Clientes</h1>
                        <p className="text-sm text-[var(--color-text-muted)]">
                            {customers.total} cliente(s) cadastrado(s)
                        </p>
                    </div>
                    <Link
                        href="/cadastros/clientes/novo"
                        className="px-4 py-2 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition-colors"
                    >
                        + Novo Cliente
                    </Link>
                </div>

                <form onSubmit={handleSearch} className="flex gap-3">
                    <input
                        type="text"
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        placeholder="Buscar por nome, documento, telefone..."
                        className="flex-1 px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
                    />
                    <select
                        value={status}
                        onChange={(e) => setStatus(e.target.value)}
                        className="px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white"
                    >
                        <option value="">Todos</option>
                        <option value="active">Ativos</option>
                        <option value="inactive">Inativos</option>
                    </select>
                    <button
                        type="submit"
                        className="px-4 py-2 rounded-lg bg-[var(--color-primary)] text-white text-sm hover:bg-[var(--color-primary-dark)] transition-colors"
                    >
                        Buscar
                    </button>
                </form>

                <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="bg-gray-50 border-b border-[var(--color-border)]">
                                    <th className="text-left px-4 py-3 font-semibold text-[var(--color-text)]">Nome</th>
                                    <th className="text-left px-4 py-3 font-semibold text-[var(--color-text)]">Documento</th>
                                    <th className="text-left px-4 py-3 font-semibold text-[var(--color-text)]">Telefone</th>
                                    <th className="text-center px-4 py-3 font-semibold text-[var(--color-text)]">Veículos</th>
                                    <th className="text-center px-4 py-3 font-semibold text-[var(--color-text)]">Status</th>
                                    <th className="text-right px-4 py-3 font-semibold text-[var(--color-text)]">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                {customers.data.length === 0 ? (
                                    <tr>
                                        <td colSpan={6} className="px-4 py-8 text-center text-[var(--color-text-muted)]">
                                            Nenhum cliente encontrado.
                                        </td>
                                    </tr>
                                ) : (
                                    customers.data.map((customer) => (
                                        <tr key={customer.id} className="border-b border-[var(--color-border)] hover:bg-gray-50 transition-colors">
                                            <td className="px-4 py-3">
                                                <Link
                                                    href={`/cadastros/clientes/${customer.id}`}
                                                    className="font-medium text-[var(--color-primary)] hover:underline"
                                                >
                                                    {customer.name}
                                                </Link>
                                                {customer.trade_name && (
                                                    <p className="text-xs text-[var(--color-text-muted)]">{customer.trade_name}</p>
                                                )}
                                            </td>
                                            <td className="px-4 py-3 text-[var(--color-text-muted)]">
                                                {customer.document || '—'}
                                            </td>
                                            <td className="px-4 py-3 text-[var(--color-text-muted)]">
                                                {customer.phone || customer.whatsapp || '—'}
                                            </td>
                                            <td className="px-4 py-3 text-center text-[var(--color-text-muted)]">
                                                {customer.vehicles_count}
                                            </td>
                                            <td className="px-4 py-3 text-center">
                                                <span className={`inline-block px-2 py-0.5 rounded-full text-xs font-medium ${
                                                    customer.status === 'active'
                                                        ? 'bg-green-100 text-green-800'
                                                        : 'bg-red-100 text-red-800'
                                                }`}>
                                                    {customer.status === 'active' ? 'Ativo' : 'Inativo'}
                                                </span>
                                            </td>
                                            <td className="px-4 py-3 text-right">
                                                <Link
                                                    href={`/cadastros/clientes/${customer.id}/editar`}
                                                    className="text-xs text-[var(--color-primary)] hover:underline"
                                                >
                                                    Editar
                                                </Link>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>

                    {customers.last_page > 1 && (
                        <div className="flex items-center justify-between px-4 py-3 border-t border-[var(--color-border)]">
                            <span className="text-xs text-[var(--color-text-muted)]">
                                Página {customers.current_page} de {customers.last_page}
                            </span>
                            <div className="flex gap-1">
                                {customers.links.map((link, i) => (
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
