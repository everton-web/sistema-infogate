import { logout } from "@/app/login/actions";
import { Sidebar } from "@/components/sidebar";
import { getAppContext } from "@/lib/auth";

export const dynamic = "force-dynamic";

export default async function DashboardLayout({ children }: { children: React.ReactNode }) {
  const { company, user, role } = await getAppContext();
  const userName = String(user.user_metadata?.name || user.email || "Usuário");
  const roleLabel = role === "owner" ? "Proprietário" : role === "admin" ? "Administrador" : "Usuário";

  return <div className="shell">
    <Sidebar />
    <div className="main">
      <header className="topbar">
        <div><strong className="company-name">{company.trade_name || company.name}</strong><span className="branch-name"> · Matriz</span></div>
        <div className="user-area">
          <div className="user-copy"><strong>{userName}</strong><span>{roleLabel}</span></div>
          <form action={logout}><button className="topbar-logout">Sair</button></form>
        </div>
      </header>
      <main className="content">{children}</main>
      <footer className="erp-footer"><span>Canal Som · Gestão Comercial</span><span>InfoGate Gestão</span></footer>
    </div>
  </div>;
}
