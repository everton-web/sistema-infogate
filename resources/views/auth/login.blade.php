<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar | InfoGate Gestão</title>
    <link rel="stylesheet" href="/assets/app.css">
</head>
<body class="login-page">

<div class="login-card">
    <div class="brand">
        <div class="brand-mark">IG</div>

        <div>
            <strong>InfoGate Gestão</strong>
            <span>Gestão comercial e empresarial</span>
        </div>
    </div>

    <h1>Acessar sistema</h1>
    <p class="muted">Entre com suas credenciais para continuar.</p>

    @if ($errors->any())
        <div class="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}">
        @csrf

        <label>E-mail</label>
        <input
            type="email"
            name="email"
            value="{{ old('email') }}"
            required
            autofocus
        >

        <label>Senha</label>
        <input
            type="password"
            name="password"
            required
        >

        <label class="remember">
            <input type="checkbox" name="remember">
            Manter conectado
        </label>

        <button type="submit">Entrar</button>
    </form>

    <footer>
        InfoGate Soluções Digitais
    </footer>
</div>

</body>
</html>
