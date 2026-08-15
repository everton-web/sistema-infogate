\
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#17191d">
    <title>@yield('title', 'Canal Som') | InfoGate Gestão</title>

    <link rel="stylesheet" href="{{ asset('assets/canalsom-erp.css') }}">
    @stack('head')
</head>
<body>
<div class="erp-shell">
    @include('partials.erp-sidebar')

    <div class="erp-main">
        <header class="erp-topbar">
            <div class="erp-topbar-left">
                <button
                    type="button"
                    class="sidebar-toggle"
                    id="sidebarToggle"
                    aria-label="Abrir menu"
                >
                    ☰
                </button>

                <div>
                    <strong class="erp-company-name">
                        {{ $currentCompany->trade_name ?? $currentCompany->name ?? 'Canal Som' }}
                    </strong>

                    @if(isset($currentBranch) && $currentBranch)
                        <span class="erp-branch-name"> · {{ $currentBranch->name }}</span>
                    @endif
                </div>
            </div>

            <div class="erp-user">
                <div class="erp-user-text">
                    <strong>{{ auth()->user()->name ?? 'Usuário' }}</strong>
                    <span>
                        {{ auth()->user()?->is_super_admin ? 'Administrador' : 'Usuário' }}
                    </span>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="topbar-logout">Sair</button>
                </form>
            </div>
        </header>

        <main class="erp-content">
            @yield('content')
        </main>

        <footer class="erp-footer">
            <span>Canal Som · Gestão Comercial</span>
            <span>InfoGate Gestão</span>
        </footer>
    </div>
</div>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const body = document.body;
    const toggle = document.getElementById('sidebarToggle');
    const overlay = document.getElementById('sidebarOverlay');

    function closeSidebar() {
        body.classList.remove('sidebar-open');
    }

    toggle?.addEventListener('click', function () {
        body.classList.toggle('sidebar-open');
    });

    overlay?.addEventListener('click', closeSidebar);

    window.addEventListener('resize', function () {
        if (window.innerWidth > 900) {
            closeSidebar();
        }
    });
});
</script>

@stack('scripts')
</body>
</html>
