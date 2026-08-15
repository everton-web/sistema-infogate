<aside class="erp-sidebar" id="erpSidebar">
    <div class="erp-brand">
        <img
            src="{{ asset('assets/canal-som-logo.png') }}"
            alt="Canal Som"
            class="erp-brand-logo"
        >

        <div class="erp-brand-copy">
            <strong>CANAL SOM</strong>
            <span>Gestão Comercial</span>
        </div>
    </div>

    <div class="sidebar-divider"></div>

    <nav class="erp-nav">
        <a href="{{ route('dashboard') }}"
           class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="nav-icon">▦</span><span>Dashboard</span>
        </a>

        <a href="#" class="nav-item nav-disabled" title="Módulo em desenvolvimento">
            <span class="nav-icon">◫</span><span>PDV</span>
        </a>

        <a href="#" class="nav-item nav-disabled" title="Módulo em desenvolvimento">
            <span class="nav-icon">▣</span><span>Caixa</span>
        </a>

        <a href="#" class="nav-item nav-disabled" title="Módulo em desenvolvimento">
            <span class="nav-icon">↗</span><span>Vendas</span>
        </a>

        <a href="#" class="nav-item nav-disabled" title="Módulo em desenvolvimento">
            <span class="nav-icon">≣</span><span>Orçamentos</span>
        </a>

        <a href="#" class="nav-item nav-disabled" title="Módulo em desenvolvimento">
            <span class="nav-icon">⚙</span><span>Ordens de Serviço</span>
        </a>

        <a href="#" class="nav-item nav-disabled" title="Módulo em desenvolvimento">
            <span class="nav-icon">✓</span><span>Garantias</span>
        </a>

        <div class="nav-section-title">CADASTROS</div>

        <a href="{{ route('customers.index') }}"
           class="nav-item {{ request()->routeIs('customers.*') ? 'active' : '' }}">
            <span class="nav-icon">●</span><span>Clientes</span>
        </a>

        <a href="{{ route('vehicles.index') }}"
           class="nav-item {{ request()->routeIs('vehicles.*') ? 'active' : '' }}">
            <span class="nav-icon">◆</span><span>Veículos</span>
        </a>

        <a href="#" class="nav-item nav-disabled" title="Módulo em desenvolvimento">
            <span class="nav-icon">▤</span><span>Produtos / Serviços</span>
        </a>

        <a href="#" class="nav-item nav-disabled" title="Módulo em desenvolvimento">
            <span class="nav-icon">⌂</span><span>Fornecedores</span>
        </a>

        <div class="nav-section-title">OPERAÇÃO</div>

        <a href="#" class="nav-item nav-disabled" title="Módulo em desenvolvimento">
            <span class="nav-icon">▥</span><span>Estoque</span>
        </a>

        <a href="#" class="nav-item nav-disabled" title="Módulo em desenvolvimento">
            <span class="nav-icon">＋</span><span>Compras</span>
        </a>

        <a href="#" class="nav-item nav-disabled" title="Módulo em desenvolvimento">
            <span class="nav-icon">$</span><span>Financeiro</span>
        </a>

        <a href="#" class="nav-item nav-disabled" title="Módulo em desenvolvimento">
            <span class="nav-icon">▧</span><span>Relatórios</span>
        </a>

        @if(auth()->user()?->is_super_admin)
            <div class="nav-section-title">ADMINISTRAÇÃO</div>

            <a href="#" class="nav-item nav-disabled" title="Módulo em desenvolvimento">
                <span class="nav-icon">♟</span><span>Usuários</span>
            </a>
        @endif
    </nav>

    <div class="erp-sidebar-bottom">
        <div class="sidebar-version">
            <span>InfoGate Gestão</span>
            <small>Canal Som · Piloto</small>
        </div>
    </div>
</aside>
