# InfoGate Gestao - Migracao para Next.js + Supabase + Vercel

## Visao Geral

Migrar o sistema de gestao InfoGate de **Laravel 13 + Inertia.js + React + SQLite** para **Next.js 15 + Supabase + Vercel**.

### Stack Atual
- **Backend:** Laravel 13 (PHP 8.3)
- **Frontend:** React 19 + Inertia.js + Tailwind CSS 4
- **Banco:** SQLite
- **Hospedagem:** cPanel (FTP deploy)

### Stack Nova
- **Fullstack:** Next.js 15 (App Router) + TypeScript
- **Frontend:** React 19 + Tailwind CSS 4 (reaproveitado)
- **Banco:** Supabase (PostgreSQL + Auth + RLS)
- **Hospedagem:** Vercel (deploy automatico via git push)

### Por que migrar?
- Deploy instantaneo com `git push` (sem FTP, sem cPanel)
- Banco PostgreSQL gerenciado com painel visual
- Autenticacao pronta (Supabase Auth)
- Multi-tenancy via Row Level Security (RLS) nativo
- Uma unica linguagem (TypeScript)
- Plano gratuito generoso (Vercel + Supabase)

---

## Inventario do Projeto Atual

### Controllers (16) e suas funcoes

| Controller | Metodos | Models |
|---|---|---|
| AuthController | create, store, destroy | - (Auth facade) |
| DashboardController | index | Customer, Vehicle |
| CustomerController | index, create, store, show, edit, update | Customer |
| VehicleController | index, create, models, store | Customer, Vehicle, VehicleBrand, VehicleModel |
| ProductController | index, create, store, show, edit, update | Product |
| SupplierController | index, create, store, show, edit, update | Supplier |
| ServiceOrderController | index, create, store, show, customerVehicles | Customer, Product, ServiceOrder, Vehicle |
| StockController | index, movements, store | Product, StockMovement |
| SaleController | index, create, store, show | Customer, Product, Sale |
| QuoteController | index, create, store, show | Customer, Product, Quote |
| WarrantyController | index, create, store, show | Customer, ServiceOrder, Vehicle, Warranty |
| PurchaseController | index, create, store, show | Product, Purchase, Supplier |
| FinancialController | index, create, store, show, pay | Customer, FinancialEntry, Supplier |
| CashRegisterController | index, open, store, show, close | CashRegister |
| ReportController | index | FinancialEntry, Purchase, Sale, SaleItem |
| UserController | index, create, store, edit, update | User |

### Tabelas do Banco (23 tabelas de negocio)

**Infraestrutura (gerenciado pelo Supabase):**
- users (auth.users do Supabase)
- sessions, cache, jobs (nao necessarios)

**Multi-tenancy:**
- companies, branches, company_settings
- company_user (pivot), branch_user (pivot)

**Cadastros:**
- customers, vehicles, vehicle_brands, vehicle_models
- products, suppliers

**Operacional:**
- service_orders, service_order_items
- sales, sale_items
- quotes, quote_items
- purchases, purchase_items
- stock_movements
- warranties

**Financeiro:**
- financial_entries
- cash_registers, cash_register_transactions

### Paginas React (43 arquivos JSX)

- Auth: Login
- Dashboard
- Customers: Index, Create, Edit, Show
- Vehicles: Index, Create
- Products: Index, Create, Edit, Show
- Suppliers: Index, Create, Edit, Show
- ServiceOrders: Index, Create, Show
- Stock: Index, Movements
- Sales: Index, Create, Show
- Quotes: Index, Create, Show
- Warranties: Index, Create, Show
- Purchases: Index, Create, Show
- Financial: Index, Create, Show
- CashRegister: Index, Open, Show
- Reports: Index
- Users: Index, Create, Edit

### Middleware atual
- **HandleInertiaRequests:** compartilha auth.user, currentCompany, currentBranch, flash
- **SetCurrentCompany:** resolve empresa ativa do usuario via pivot

---

## Mapeamento da Migracao

### Estrutura Next.js (App Router)

```
src/
  app/
    layout.tsx                    # Layout raiz + providers
    login/page.tsx                # Auth: Login
    (dashboard)/
      layout.tsx                  # Layout autenticado (sidebar, header)
      page.tsx                    # Dashboard
      cadastros/
        clientes/
          page.tsx                # Customers Index
          novo/page.tsx           # Customers Create
          [id]/page.tsx           # Customers Show
          [id]/editar/page.tsx    # Customers Edit
        veiculos/
          page.tsx                # Vehicles Index
          novo/page.tsx           # Vehicles Create
        produtos/
          page.tsx                # Products Index
          novo/page.tsx           # Products Create
          [id]/page.tsx           # Products Show
          [id]/editar/page.tsx    # Products Edit
        fornecedores/
          page.tsx                # Suppliers Index
          novo/page.tsx           # Suppliers Create
          [id]/page.tsx           # Suppliers Show
          [id]/editar/page.tsx    # Suppliers Edit
      ordens-servico/
        page.tsx                  # ServiceOrders Index
        nova/page.tsx             # ServiceOrders Create
        [id]/page.tsx             # ServiceOrders Show
      estoque/
        page.tsx                  # Stock Index
        [id]/movimentacoes/page.tsx  # Stock Movements
      vendas/
        page.tsx                  # Sales Index
        nova/page.tsx             # Sales Create
        [id]/page.tsx             # Sales Show
      orcamentos/
        page.tsx                  # Quotes Index
        novo/page.tsx             # Quotes Create
        [id]/page.tsx             # Quotes Show
      garantias/
        page.tsx                  # Warranties Index
        nova/page.tsx             # Warranties Create
        [id]/page.tsx             # Warranties Show
      compras/
        page.tsx                  # Purchases Index
        nova/page.tsx             # Purchases Create
        [id]/page.tsx             # Purchases Show
      financeiro/
        page.tsx                  # Financial Index
        novo/page.tsx             # Financial Create
        [id]/page.tsx             # Financial Show
      caixa/
        page.tsx                  # CashRegister Index
        abrir/page.tsx            # CashRegister Open
        [id]/page.tsx             # CashRegister Show
      relatorios/page.tsx         # Reports Index
      admin/
        usuarios/
          page.tsx                # Users Index
          novo/page.tsx           # Users Create
          [id]/editar/page.tsx    # Users Edit
  lib/
    supabase/
      client.ts                   # Supabase browser client
      server.ts                   # Supabase server client
      middleware.ts               # Auth middleware
    types/
      database.ts                 # Tipos gerados do Supabase
  components/
    ui/                           # Componentes reutilizaveis
    layouts/
      AuthLayout.tsx              # Layout de login
      DashboardLayout.tsx         # Layout principal (sidebar + header)
  middleware.ts                   # Next.js middleware (auth guard)
```

### Mapeamento Controller -> API Route / Server Action

| Laravel Controller | Next.js equivalente |
|---|---|
| AuthController | Supabase Auth (signIn, signOut) - sem API route |
| DashboardController | Server Component com query direta |
| CustomerController | app/api/customers/route.ts + Server Components |
| VehicleController | app/api/vehicles/route.ts + Server Components |
| ProductController | app/api/products/route.ts + Server Components |
| SupplierController | app/api/suppliers/route.ts + Server Components |
| ServiceOrderController | app/api/service-orders/route.ts + Server Components |
| StockController | app/api/stock/route.ts + Server Components |
| SaleController | app/api/sales/route.ts + Server Components |
| QuoteController | app/api/quotes/route.ts + Server Components |
| WarrantyController | app/api/warranties/route.ts + Server Components |
| PurchaseController | app/api/purchases/route.ts + Server Components |
| FinancialController | app/api/financial/route.ts + Server Components |
| CashRegisterController | app/api/cash-register/route.ts + Server Components |
| ReportController | Server Component com queries diretas |
| UserController | app/api/users/route.ts + Server Components |

### Mapeamento Middleware

| Laravel | Next.js + Supabase |
|---|---|
| HandleInertiaRequests (share auth/company) | React Context (AuthProvider, CompanyProvider) |
| SetCurrentCompany | middleware.ts + cookie/header com company_id |
| Auth guard | Supabase middleware (verifica sessao) |

### Supabase RLS (Row Level Security)

Multi-tenancy sera implementado via RLS. Exemplo para tabela `customers`:

```sql
-- Usuarios so veem clientes da sua empresa
CREATE POLICY "Users can view own company customers"
ON customers FOR SELECT
USING (company_id IN (
  SELECT company_id FROM company_user
  WHERE user_id = auth.uid() AND is_active = true
));
```

Aplicar pattern similar em todas as tabelas que tem `company_id`.

---

## Schema SQL Supabase

```sql
-- Empresas
CREATE TABLE companies (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  name TEXT NOT NULL,
  trade_name TEXT,
  slug TEXT UNIQUE NOT NULL,
  document TEXT,
  email TEXT,
  phone TEXT,
  plan TEXT DEFAULT 'basic',
  status TEXT DEFAULT 'active',
  logo_path TEXT,
  timezone TEXT DEFAULT 'America/Sao_Paulo',
  locale TEXT DEFAULT 'pt-BR',
  currency TEXT DEFAULT 'BRL',
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now()
);

-- Filiais
CREATE TABLE branches (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  company_id UUID REFERENCES companies(id) ON DELETE CASCADE NOT NULL,
  name TEXT NOT NULL,
  code TEXT,
  document TEXT,
  email TEXT,
  phone TEXT,
  postal_code TEXT,
  street TEXT,
  number TEXT,
  complement TEXT,
  neighborhood TEXT,
  city TEXT,
  state TEXT,
  is_main BOOLEAN DEFAULT false,
  status TEXT DEFAULT 'active',
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now()
);

-- Configuracoes da empresa
CREATE TABLE company_settings (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  company_id UUID REFERENCES companies(id) ON DELETE CASCADE NOT NULL,
  setting_key TEXT NOT NULL,
  setting_value TEXT,
  setting_type TEXT DEFAULT 'string',
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now(),
  UNIQUE(company_id, setting_key)
);

-- Pivot empresa-usuario
CREATE TABLE company_user (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  company_id UUID REFERENCES companies(id) ON DELETE CASCADE NOT NULL,
  user_id UUID REFERENCES auth.users(id) ON DELETE CASCADE NOT NULL,
  role TEXT DEFAULT 'user',
  is_active BOOLEAN DEFAULT true,
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now(),
  UNIQUE(company_id, user_id)
);

-- Pivot filial-usuario
CREATE TABLE branch_user (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  branch_id UUID REFERENCES branches(id) ON DELETE CASCADE NOT NULL,
  user_id UUID REFERENCES auth.users(id) ON DELETE CASCADE NOT NULL,
  is_active BOOLEAN DEFAULT true,
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now(),
  UNIQUE(branch_id, user_id)
);

-- Clientes
CREATE TABLE customers (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  company_id UUID REFERENCES companies(id) ON DELETE CASCADE NOT NULL,
  type TEXT DEFAULT 'pf',
  name TEXT NOT NULL,
  trade_name TEXT,
  document TEXT,
  state_registration TEXT,
  phone TEXT,
  whatsapp TEXT,
  email TEXT,
  postal_code TEXT,
  street TEXT,
  number TEXT,
  complement TEXT,
  neighborhood TEXT,
  city TEXT,
  state TEXT,
  notes TEXT,
  status TEXT DEFAULT 'active',
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now()
);

-- Marcas de veiculos
CREATE TABLE vehicle_brands (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  name TEXT UNIQUE NOT NULL,
  external_code TEXT UNIQUE,
  source TEXT DEFAULT 'fipe',
  is_active BOOLEAN DEFAULT true,
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now()
);

-- Modelos de veiculos
CREATE TABLE vehicle_models (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  vehicle_brand_id UUID REFERENCES vehicle_brands(id) ON DELETE CASCADE NOT NULL,
  name TEXT NOT NULL,
  external_code TEXT,
  source TEXT DEFAULT 'fipe',
  is_active BOOLEAN DEFAULT true,
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now(),
  UNIQUE(vehicle_brand_id, name),
  UNIQUE(vehicle_brand_id, external_code)
);

-- Veiculos
CREATE TABLE vehicles (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  company_id UUID REFERENCES companies(id) ON DELETE CASCADE NOT NULL,
  customer_id UUID REFERENCES customers(id) ON DELETE CASCADE NOT NULL,
  vehicle_brand_id UUID REFERENCES vehicle_brands(id) ON DELETE RESTRICT,
  vehicle_model_id UUID REFERENCES vehicle_models(id) ON DELETE RESTRICT,
  plate TEXT NOT NULL,
  version TEXT,
  year_manufacture TEXT,
  year_model TEXT,
  color TEXT,
  chassis TEXT,
  odometer INTEGER,
  notes TEXT,
  status TEXT DEFAULT 'active',
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now(),
  UNIQUE(company_id, plate)
);

-- Produtos e servicos
CREATE TABLE products (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  company_id UUID REFERENCES companies(id) ON DELETE CASCADE NOT NULL,
  type TEXT NOT NULL CHECK (type IN ('product', 'service')),
  name TEXT NOT NULL,
  sku TEXT,
  barcode TEXT,
  description TEXT,
  unit TEXT DEFAULT 'UN',
  cost_price DECIMAL(12,2),
  sale_price DECIMAL(12,2),
  stock_quantity DECIMAL(12,3) DEFAULT 0,
  stock_minimum DECIMAL(12,3) DEFAULT 0,
  category TEXT,
  brand TEXT,
  ncm TEXT,
  status TEXT DEFAULT 'active' CHECK (status IN ('active', 'inactive')),
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now(),
  UNIQUE(company_id, sku)
);

-- Fornecedores
CREATE TABLE suppliers (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  company_id UUID REFERENCES companies(id) ON DELETE CASCADE NOT NULL,
  type TEXT DEFAULT 'pj' CHECK (type IN ('pf', 'pj')),
  name TEXT NOT NULL,
  trade_name TEXT,
  document TEXT,
  state_registration TEXT,
  phone TEXT,
  whatsapp TEXT,
  email TEXT,
  contact_name TEXT,
  postal_code TEXT,
  street TEXT,
  number TEXT,
  complement TEXT,
  neighborhood TEXT,
  city TEXT,
  state TEXT,
  notes TEXT,
  status TEXT DEFAULT 'active' CHECK (status IN ('active', 'inactive')),
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now()
);

-- Ordens de servico
CREATE TABLE service_orders (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  company_id UUID REFERENCES companies(id) ON DELETE CASCADE NOT NULL,
  branch_id UUID REFERENCES branches(id) ON DELETE SET NULL,
  customer_id UUID REFERENCES customers(id) ON DELETE CASCADE NOT NULL,
  vehicle_id UUID REFERENCES vehicles(id) ON DELETE SET NULL,
  number INTEGER NOT NULL,
  status TEXT DEFAULT 'draft' CHECK (status IN ('draft', 'open', 'in_progress', 'completed', 'cancelled')),
  complaint TEXT,
  diagnosis TEXT,
  internal_notes TEXT,
  discount DECIMAL(12,2) DEFAULT 0,
  total DECIMAL(12,2) DEFAULT 0,
  opened_at TIMESTAMPTZ,
  completed_at TIMESTAMPTZ,
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now(),
  UNIQUE(company_id, number)
);

CREATE TABLE service_order_items (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  service_order_id UUID REFERENCES service_orders(id) ON DELETE CASCADE NOT NULL,
  product_id UUID REFERENCES products(id) ON DELETE SET NULL,
  type TEXT CHECK (type IN ('product', 'service')),
  description TEXT,
  quantity DECIMAL(12,3) DEFAULT 1,
  unit_price DECIMAL(12,2) DEFAULT 0,
  discount DECIMAL(12,2) DEFAULT 0,
  total DECIMAL(12,2) DEFAULT 0,
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now()
);

-- Movimentacoes de estoque
CREATE TABLE stock_movements (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  company_id UUID REFERENCES companies(id) ON DELETE CASCADE NOT NULL,
  product_id UUID REFERENCES products(id) ON DELETE CASCADE NOT NULL,
  type TEXT NOT NULL CHECK (type IN ('entry', 'exit', 'adjustment')),
  quantity DECIMAL(12,3) NOT NULL,
  unit_cost DECIMAL(12,2),
  reason TEXT,
  reference_type TEXT,
  reference_id UUID,
  user_id UUID REFERENCES auth.users(id) ON DELETE SET NULL,
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now()
);

-- Vendas
CREATE TABLE sales (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  company_id UUID REFERENCES companies(id) ON DELETE CASCADE NOT NULL,
  branch_id UUID REFERENCES branches(id) ON DELETE SET NULL,
  customer_id UUID REFERENCES customers(id) ON DELETE SET NULL,
  user_id UUID REFERENCES auth.users(id) ON DELETE SET NULL,
  number INTEGER NOT NULL,
  status TEXT DEFAULT 'open' CHECK (status IN ('open', 'completed', 'cancelled')),
  payment_method TEXT CHECK (payment_method IN ('cash', 'credit_card', 'debit_card', 'pix', 'boleto', 'other')),
  subtotal DECIMAL(12,2) DEFAULT 0,
  discount DECIMAL(12,2) DEFAULT 0,
  total DECIMAL(12,2) DEFAULT 0,
  notes TEXT,
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now(),
  UNIQUE(company_id, number)
);

CREATE TABLE sale_items (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  sale_id UUID REFERENCES sales(id) ON DELETE CASCADE NOT NULL,
  product_id UUID REFERENCES products(id) ON DELETE SET NULL,
  description TEXT,
  quantity DECIMAL(12,3) DEFAULT 1,
  unit_price DECIMAL(12,2) DEFAULT 0,
  discount DECIMAL(12,2) DEFAULT 0,
  total DECIMAL(12,2) DEFAULT 0,
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now()
);

-- Orcamentos
CREATE TABLE quotes (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  company_id UUID REFERENCES companies(id) ON DELETE CASCADE NOT NULL,
  customer_id UUID REFERENCES customers(id) ON DELETE SET NULL,
  vehicle_id UUID REFERENCES vehicles(id) ON DELETE SET NULL,
  number INTEGER NOT NULL,
  status TEXT DEFAULT 'draft' CHECK (status IN ('draft', 'sent', 'approved', 'rejected', 'expired')),
  valid_until DATE,
  subtotal DECIMAL(12,2) DEFAULT 0,
  discount DECIMAL(12,2) DEFAULT 0,
  total DECIMAL(12,2) DEFAULT 0,
  notes TEXT,
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now(),
  UNIQUE(company_id, number)
);

CREATE TABLE quote_items (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  quote_id UUID REFERENCES quotes(id) ON DELETE CASCADE NOT NULL,
  product_id UUID REFERENCES products(id) ON DELETE SET NULL,
  type TEXT CHECK (type IN ('product', 'service')),
  description TEXT,
  quantity DECIMAL(12,3) DEFAULT 1,
  unit_price DECIMAL(12,2) DEFAULT 0,
  discount DECIMAL(12,2) DEFAULT 0,
  total DECIMAL(12,2) DEFAULT 0,
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now()
);

-- Garantias
CREATE TABLE warranties (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  company_id UUID REFERENCES companies(id) ON DELETE CASCADE NOT NULL,
  customer_id UUID REFERENCES customers(id) ON DELETE CASCADE NOT NULL,
  vehicle_id UUID REFERENCES vehicles(id) ON DELETE SET NULL,
  service_order_id UUID REFERENCES service_orders(id) ON DELETE SET NULL,
  number INTEGER NOT NULL,
  description TEXT,
  status TEXT DEFAULT 'active' CHECK (status IN ('active', 'claimed', 'expired', 'voided')),
  start_date DATE,
  end_date DATE,
  terms TEXT,
  claim_notes TEXT,
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now(),
  UNIQUE(company_id, number)
);

-- Compras
CREATE TABLE purchases (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  company_id UUID REFERENCES companies(id) ON DELETE CASCADE NOT NULL,
  supplier_id UUID REFERENCES suppliers(id) ON DELETE SET NULL,
  number INTEGER NOT NULL,
  invoice_number TEXT,
  status TEXT DEFAULT 'draft' CHECK (status IN ('draft', 'ordered', 'received', 'cancelled')),
  subtotal DECIMAL(12,2) DEFAULT 0,
  discount DECIMAL(12,2) DEFAULT 0,
  shipping DECIMAL(12,2) DEFAULT 0,
  total DECIMAL(12,2) DEFAULT 0,
  expected_date DATE,
  received_date DATE,
  notes TEXT,
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now(),
  UNIQUE(company_id, number)
);

CREATE TABLE purchase_items (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  purchase_id UUID REFERENCES purchases(id) ON DELETE CASCADE NOT NULL,
  product_id UUID REFERENCES products(id) ON DELETE SET NULL,
  description TEXT,
  quantity DECIMAL(12,3) DEFAULT 1,
  unit_cost DECIMAL(12,2) DEFAULT 0,
  total DECIMAL(12,2) DEFAULT 0,
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now()
);

-- Financeiro
CREATE TABLE financial_entries (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  company_id UUID REFERENCES companies(id) ON DELETE CASCADE NOT NULL,
  type TEXT NOT NULL CHECK (type IN ('receivable', 'payable')),
  status TEXT DEFAULT 'pending' CHECK (status IN ('pending', 'paid', 'overdue', 'cancelled')),
  description TEXT,
  category TEXT,
  amount DECIMAL(12,2) NOT NULL,
  paid_amount DECIMAL(12,2),
  due_date DATE,
  paid_date DATE,
  payment_method TEXT CHECK (payment_method IN ('cash', 'credit_card', 'debit_card', 'pix', 'boleto', 'other')),
  reference_type TEXT,
  reference_id UUID,
  customer_id UUID REFERENCES customers(id) ON DELETE SET NULL,
  supplier_id UUID REFERENCES suppliers(id) ON DELETE SET NULL,
  notes TEXT,
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now()
);

-- Caixa
CREATE TABLE cash_registers (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  company_id UUID REFERENCES companies(id) ON DELETE CASCADE NOT NULL,
  branch_id UUID REFERENCES branches(id) ON DELETE SET NULL,
  user_id UUID REFERENCES auth.users(id) ON DELETE CASCADE NOT NULL,
  opening_balance DECIMAL(12,2) DEFAULT 0,
  closing_balance DECIMAL(12,2),
  status TEXT DEFAULT 'open' CHECK (status IN ('open', 'closed')),
  opened_at TIMESTAMPTZ,
  closed_at TIMESTAMPTZ,
  notes TEXT,
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now()
);

CREATE TABLE cash_register_transactions (
  id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
  cash_register_id UUID REFERENCES cash_registers(id) ON DELETE CASCADE NOT NULL,
  type TEXT CHECK (type IN ('sale', 'payment', 'withdrawal', 'deposit', 'adjustment')),
  description TEXT,
  amount DECIMAL(12,2) NOT NULL,
  payment_method TEXT DEFAULT 'cash' CHECK (payment_method IN ('cash', 'credit_card', 'debit_card', 'pix', 'boleto', 'other')),
  reference_type TEXT,
  reference_id UUID,
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now()
);
```

---

## Dependencias do Projeto Next.js

```json
{
  "dependencies": {
    "next": "^15",
    "react": "^19",
    "react-dom": "^19",
    "@supabase/supabase-js": "^2",
    "@supabase/ssr": "^0.5",
    "tailwindcss": "^4"
  },
  "devDependencies": {
    "typescript": "^5",
    "@types/react": "^19",
    "@types/node": "^22",
    "supabase": "^1"
  }
}
```

---

## Variaveis de Ambiente

```env
NEXT_PUBLIC_SUPABASE_URL=https://xxxxx.supabase.co
NEXT_PUBLIC_SUPABASE_ANON_KEY=eyJhbGci...
SUPABASE_SERVICE_ROLE_KEY=eyJhbGci...
```

---

## Estimativa por Modulo

| Modulo | Complexidade | Paginas | Prioridade |
|---|---|---|---|
| Auth (login/logout) | Baixa | 1 | 1 - Primeiro |
| Layout (sidebar/header) | Media | 1 | 2 |
| Dashboard | Baixa | 1 | 3 |
| Clientes | Media | 4 | 4 |
| Veiculos | Media | 2 | 5 |
| Produtos | Media | 4 | 6 |
| Fornecedores | Media | 4 | 7 |
| Ordens de Servico | Alta | 3 | 8 |
| Estoque | Media | 2 | 9 |
| Vendas | Alta | 3 | 10 |
| Orcamentos | Alta | 3 | 11 |
| Garantias | Media | 3 | 12 |
| Compras | Alta | 3 | 13 |
| Financeiro | Alta | 3 | 14 |
| Caixa | Alta | 3 | 15 |
| Relatorios | Media | 1 | 16 |
| Usuarios | Media | 3 | 17 |
