import { Link, router, Head } from '@inertiajs/react';
import { useState } from 'react';

const roleLabels = { owner: 'Proprietário', admin: 'Administrador', manager: 'Gerente', user: 'Usuário' };

export default function UsersIndex({ users, filters }) {
    const [search, setSearch] = useState(filters?.q || '');
    function handleSearch(e) { e.preventDefault(); router.get('/admin/usuarios', { q: search }, { preserveState: true }); }

    return (
        <>
            <Head title="Usuários" />
            <div className="space-y-4">
                <div className="flex items-center justify-between">
                    <div><h1 className="text-xl font-bold text-[var(--color-text)]">Usuários</h1><p className="text-sm text-[var(--color-text-muted)]">{users.total} usuário(s)</p></div>
                    <Link href="/admin/usuarios/novo" className="px-4 py-2 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition-colors">+ Novo Usuário</Link>
                </div>
                <form onSubmit={handleSearch} className="flex gap-3">
                    <input type="text" value={search} onChange={(e) => setSearch(e.target.value)} placeholder="Buscar por nome ou e-mail..." className="flex-1 px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]" />
                    <button type="submit" className="px-4 py-2 rounded-lg bg-[var(--color-primary)] text-white text-sm hover:bg-[var(--color-primary-dark)] transition-colors">Buscar</button>
                </form>
                <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead><tr className="bg-gray-50 border-b border-[var(--color-border)]">
                                <th className="text-left px-4 py-3 font-semibold text-[var(--color-text)]">Nome</th>
                                <th className="text-left px-4 py-3 font-semibold text-[var(--color-text)]">E-mail</th>
                                <th className="text-center px-4 py-3 font-semibold text-[var(--color-text)]">Perfil</th>
                                <th className="text-center px-4 py-3 font-semibold text-[var(--color-text)]">Status</th>
                                <th className="text-right px-4 py-3 font-semibold text-[var(--color-text)]">Ações</th>
                            </tr></thead>
                            <tbody>
                                {users.data.length === 0 ? (
                                    <tr><td colSpan={5} className="px-4 py-8 text-center text-[var(--color-text-muted)]">Nenhum usuário encontrado.</td></tr>
                                ) : users.data.map((u) => (
                                    <tr key={u.id} className="border-b border-[var(--color-border)] hover:bg-gray-50 transition-colors">
                                        <td className="px-4 py-3">
                                            <div className="flex items-center gap-2">
                                                <span className="font-medium text-[var(--color-text)]">{u.name}</span>
                                                {u.is_super_admin && <span className="inline-block px-1.5 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800">SUPER</span>}
                                            </div>
                                        </td>
                                        <td className="px-4 py-3 text-[var(--color-text-muted)]">{u.email}</td>
                                        <td className="px-4 py-3 text-center"><span className="inline-block px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{roleLabels[u.role] || u.role}</span></td>
                                        <td className="px-4 py-3 text-center"><span className={`inline-block px-2 py-0.5 rounded-full text-xs font-medium ${u.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>{u.is_active ? 'Ativo' : 'Inativo'}</span></td>
                                        <td className="px-4 py-3 text-right"><Link href={`/admin/usuarios/${u.id}/editar`} className="text-xs text-[var(--color-primary)] hover:underline">Editar</Link></td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                    {users.last_page > 1 && (
                        <div className="flex items-center justify-between px-4 py-3 border-t border-[var(--color-border)]">
                            <span className="text-xs text-[var(--color-text-muted)]">Página {users.current_page} de {users.last_page}</span>
                            <div className="flex gap-1">{users.links.map((link, i) => (<Link key={i} href={link.url || '#'} className={`px-3 py-1 rounded text-xs ${link.active ? 'bg-[var(--color-primary)] text-white' : link.url ? 'text-[var(--color-text-muted)] hover:bg-gray-100' : 'text-gray-300 cursor-not-allowed'}`} dangerouslySetInnerHTML={{ __html: link.label }} />))}</div>
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}
