import { Link, usePage, router } from '@inertiajs/react';
import { useState } from 'react';

const navItems = [
    { label: 'Dashboard', href: '/', icon: '▦', match: 'dashboard' },
    { label: 'Caixa', href: '/caixa', icon: '▣', match: 'caixa' },
    { label: 'Vendas', href: '/vendas', icon: '↗', match: 'vendas' },
    { label: 'Orçamentos', href: '/orcamentos', icon: '≣', match: 'orcamentos' },
    { label: 'Ordens de Serviço', href: '/ordens-servico', icon: '⚙', match: 'ordens-servico' },
    { label: 'Garantias', href: '/garantias', icon: '✓', match: 'garantias' },
];

const cadastroItems = [
    { label: 'Clientes', href: '/cadastros/clientes', icon: '●', match: 'customers' },
    { label: 'Veículos', href: '/cadastros/veiculos', icon: '◆', match: 'vehicles' },
    { label: 'Produtos / Serviços', href: '/cadastros/produtos', icon: '▤', match: 'products' },
    { label: 'Fornecedores', href: '/cadastros/fornecedores', icon: '⌂', match: 'suppliers' },
];

const operacaoItems = [
    { label: 'Estoque', href: '/estoque', icon: '▥', match: 'estoque' },
    { label: 'Compras', href: '/compras', icon: '＋', match: 'compras' },
    { label: 'Financeiro', href: '/financeiro', icon: '$', match: 'financeiro' },
    { label: 'Relatórios', href: '/relatorios', icon: '▧', match: 'relatorios' },
];

function NavItem({ item, currentUrl }) {
    const isDashboardActive = item.match === 'dashboard' && currentUrl === '/';
    const isActive = !isDashboardActive && item.href && item.href !== '/' && currentUrl.startsWith(item.href);

    if (item.disabled) {
        return (
            <span className="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-500 cursor-not-allowed opacity-50">
                <span className="w-5 text-center text-xs">{item.icon}</span>
                <span>{item.label}</span>
            </span>
        );
    }

    return (
        <Link
            href={item.href}
            className={`flex items-center gap-3 px-4 py-2.5 text-sm rounded-lg transition-colors ${
                isActive || isDashboardActive
                    ? 'bg-[var(--color-sidebar-hover)] text-white font-medium'
                    : 'text-gray-300 hover:bg-[var(--color-sidebar-hover)] hover:text-white'
            }`}
        >
            <span className="w-5 text-center text-xs">{item.icon}</span>
            <span>{item.label}</span>
        </Link>
    );
}

export default function AuthLayout({ children }) {
    const { auth, currentCompany, currentBranch, flash } = usePage().props;
    const currentUrl = usePage().url;
    const [sidebarOpen, setSidebarOpen] = useState(false);

    function handleLogout(e) {
        e.preventDefault();
        router.post('/logout');
    }

    return (
        <div className="flex h-screen bg-[var(--color-background)]">
            {sidebarOpen && (
                <div
                    className="fixed inset-0 z-40 bg-black/50 lg:hidden"
                    onClick={() => setSidebarOpen(false)}
                />
            )}

            <aside
                className={`fixed inset-y-0 left-0 z-50 w-64 bg-[var(--color-sidebar)] flex flex-col transition-transform lg:translate-x-0 lg:static lg:z-auto ${
                    sidebarOpen ? 'translate-x-0' : '-translate-x-full'
                }`}
            >
                <div className="flex items-center gap-3 px-5 py-5 border-b border-gray-700">
                    <img
                        src="/assets/canal-som-logo.png"
                        alt="Canal Som"
                        className="w-9 h-9 rounded-lg"
                    />
                    <div className="text-white">
                        <strong className="block text-sm font-bold">CANAL SOM</strong>
                        <span className="text-xs text-gray-400">Gestão Comercial</span>
                    </div>
                </div>

                <nav className="flex-1 overflow-y-auto px-3 py-4 space-y-1">
                    {navItems.map((item) => (
                        <NavItem key={item.label} item={item} currentUrl={currentUrl} />
                    ))}

                    <div className="pt-4 pb-1 px-4">
                        <span className="text-[10px] font-semibold tracking-wider text-gray-500 uppercase">
                            Cadastros
                        </span>
                    </div>

                    {cadastroItems.map((item) => (
                        <NavItem key={item.label} item={item} currentUrl={currentUrl} />
                    ))}

                    <div className="pt-4 pb-1 px-4">
                        <span className="text-[10px] font-semibold tracking-wider text-gray-500 uppercase">
                            Operação
                        </span>
                    </div>

                    {operacaoItems.map((item) => (
                        <NavItem key={item.label} item={item} currentUrl={currentUrl} />
                    ))}

                    {auth?.user?.is_super_admin && (
                        <>
                            <div className="pt-4 pb-1 px-4">
                                <span className="text-[10px] font-semibold tracking-wider text-gray-500 uppercase">
                                    Administração
                                </span>
                            </div>
                            <NavItem
                                item={{ label: 'Usuários', href: '/admin/usuarios', icon: '♟', match: 'admin/usuarios' }}
                                currentUrl={currentUrl}
                            />
                        </>
                    )}
                </nav>

                <div className="px-5 py-4 border-t border-gray-700 text-center">
                    <span className="text-xs text-gray-500">InfoGate Gestão</span>
                    <br />
                    <span className="text-[10px] text-gray-600">Canal Som · Piloto</span>
                </div>
            </aside>

            <div className="flex-1 flex flex-col min-w-0">
                <header className="flex items-center justify-between px-6 py-3 bg-white border-b border-[var(--color-border)] shrink-0">
                    <div className="flex items-center gap-4">
                        <button
                            onClick={() => setSidebarOpen(true)}
                            className="lg:hidden text-xl text-gray-600"
                        >
                            ☰
                        </button>

                        <div>
                            <strong className="text-sm font-semibold text-[var(--color-text)]">
                                {currentCompany?.trade_name || currentCompany?.name || 'Canal Som'}
                            </strong>
                            {currentBranch && (
                                <span className="text-sm text-[var(--color-text-muted)]">
                                    {' '}· {currentBranch.name}
                                </span>
                            )}
                        </div>
                    </div>

                    <div className="flex items-center gap-4">
                        <div className="text-right hidden sm:block">
                            <strong className="text-sm text-[var(--color-text)]">
                                {auth?.user?.name || 'Usuário'}
                            </strong>
                            <br />
                            <span className="text-xs text-[var(--color-text-muted)]">
                                {auth?.user?.is_super_admin ? 'Administrador' : 'Usuário'}
                            </span>
                        </div>

                        <button
                            onClick={handleLogout}
                            className="text-sm text-[var(--color-text-muted)] hover:text-[var(--color-danger)] transition-colors"
                        >
                            Sair
                        </button>
                    </div>
                </header>

                <main id="page-content" className="flex-1 overflow-y-auto p-6 transition-opacity duration-150">
                    {flash?.success && (
                        <div className="mb-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm">
                            {flash.success}
                        </div>
                    )}

                    {flash?.error && (
                        <div className="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm">
                            {flash.error}
                        </div>
                    )}

                    {children}
                </main>

                <footer className="flex items-center justify-between px-6 py-3 border-t border-[var(--color-border)] text-xs text-[var(--color-text-muted)] shrink-0">
                    <span>Canal Som · Gestão Comercial</span>
                    <span>InfoGate Gestão</span>
                </footer>
            </div>
        </div>
    );
}
