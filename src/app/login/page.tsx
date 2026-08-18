import { login } from "./actions";

export default async function LoginPage({ searchParams }: { searchParams: Promise<{ erro?: string }> }) {
  const { erro } = await searchParams;
  return <main className="login-page">
    <form action={login} className="login-card">
      <div className="login-brand">
        <span className="login-brand-mark">IG</span>
        <div><strong>InfoGate Gestão</strong><span>Gestão comercial e empresarial</span></div>
      </div>
      <h1>Acessar sistema</h1>
      <p className="muted">Entre com suas credenciais para continuar.</p>
      {erro && <p className="error">{erro}</p>}
      <div className="field"><label htmlFor="email">E-mail</label><input className="input" id="email" name="email" type="email" autoComplete="email" required /></div>
      <div className="field login-password"><label htmlFor="password">Senha</label><input className="input" id="password" name="password" type="password" autoComplete="current-password" required /></div>
      <button className="btn btn-primary login-submit" type="submit">Entrar</button>
      <footer>InfoGate Soluções Digitais</footer>
    </form>
  </main>;
}
