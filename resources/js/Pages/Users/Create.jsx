import { useForm, Link, Head } from '@inertiajs/react';

export default function UserCreate() {
    const { data, setData, post, processing, errors } = useForm({
        name: '', email: '', password: '', role: 'user',
    });
    function handleSubmit(e) { e.preventDefault(); post('/admin/usuarios'); }

    return (
        <>
            <Head title="Novo Usuário" />
            <div className="space-y-6">
                <div>
                    <Link href="/admin/usuarios" className="text-xs text-[var(--color-primary)] hover:underline">← Voltar</Link>
                    <h1 className="text-xl font-bold text-[var(--color-text)] mt-1">Novo Usuário</h1>
                </div>
                <form onSubmit={handleSubmit} className="max-w-lg space-y-6">
                    <div className="bg-[var(--color-surface)] rounded-xl border border-[var(--color-border)] p-5 space-y-4">
                        <div>
                            <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Nome *</label>
                            <input type="text" value={data.name} onChange={(e) => setData('name', e.target.value)} className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white" />
                            {errors.name && <p className="text-xs text-[var(--color-danger)] mt-1">{errors.name}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-[var(--color-text)] mb-1">E-mail *</label>
                            <input type="email" value={data.email} onChange={(e) => setData('email', e.target.value)} className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white" />
                            {errors.email && <p className="text-xs text-[var(--color-danger)] mt-1">{errors.email}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Senha *</label>
                            <input type="password" value={data.password} onChange={(e) => setData('password', e.target.value)} className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white" />
                            {errors.password && <p className="text-xs text-[var(--color-danger)] mt-1">{errors.password}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-[var(--color-text)] mb-1">Perfil *</label>
                            <select value={data.role} onChange={(e) => setData('role', e.target.value)} className="w-full px-3 py-2 rounded-lg border border-[var(--color-border)] text-sm bg-white">
                                <option value="user">Usuário</option>
                                <option value="manager">Gerente</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                    </div>
                    <div className="flex gap-3">
                        <button type="submit" disabled={processing} className="px-6 py-2.5 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition-colors disabled:opacity-50">{processing ? 'Salvando...' : 'Criar Usuário'}</button>
                        <Link href="/admin/usuarios" className="px-6 py-2.5 rounded-lg border border-[var(--color-border)] text-sm text-[var(--color-text-muted)] hover:bg-gray-50 transition-colors">Cancelar</Link>
                    </div>
                </form>
            </div>
        </>
    );
}
