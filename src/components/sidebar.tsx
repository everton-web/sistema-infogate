"use client";

import Image from "next/image";
import Link from "next/link";
import { usePathname } from "next/navigation";
import {
  BarChart3, Building2, Car, FileText, Landmark, LayoutDashboard, Package,
  PackageCheck, ShieldCheck, ShoppingCart, Truck, UserCog, Users, WalletCards,
  Warehouse, Wrench,
} from "lucide-react";

const groups = [
  { label: "", links: [["/", "Dashboard", LayoutDashboard]] },
  { label: "Cadastros", links: [
    ["/clientes", "Clientes", Users], ["/veiculos", "Veículos", Car],
    ["/produtos", "Produtos / Serviços", Package], ["/fornecedores", "Fornecedores", Truck],
  ] },
  { label: "Operação", links: [
    ["/ordens-servico", "Ordens de Serviço", Wrench], ["/estoque", "Estoque", Warehouse],
    ["/vendas", "Vendas", ShoppingCart], ["/orcamentos", "Orçamentos", FileText],
    ["/garantias", "Garantias", ShieldCheck], ["/compras", "Compras", PackageCheck],
    ["/financeiro", "Financeiro", Landmark], ["/caixa", "Caixa", WalletCards],
    ["/relatorios", "Relatórios", BarChart3],
  ] },
  { label: "Administração", links: [
    ["/filiais", "Filiais", Building2], ["/usuarios", "Usuários", UserCog],
  ] },
] as const;

export function Sidebar() {
  const pathname = usePathname();
  return <aside className="sidebar">
    <div className="erp-brand">
      <Image className="erp-brand-logo" src="/canal-som-logo.png" alt="Canal Som" width={58} height={58} priority />
      <div className="erp-brand-copy"><strong>CANAL SOM</strong><span>Gestão Comercial</span></div>
    </div>
    <div className="sidebar-divider" />
    <nav>{groups.map(group => <div key={group.label || "dashboard"}>
      {group.label && <div className="nav-group">{group.label}</div>}
      {group.links.map(([href, label, Icon]) => {
        const active = href === "/" ? pathname === "/" : pathname.startsWith(href);
        return <Link className={`nav-link${active ? " active" : ""}`} href={href} key={href}>
          <Icon size={18}/><span>{label}</span>
        </Link>;
      })}
    </div>)}</nav>
    <div className="sidebar-version"><span>InfoGate Gestão</span><small>Canal Som · Piloto</small></div>
  </aside>;
}
