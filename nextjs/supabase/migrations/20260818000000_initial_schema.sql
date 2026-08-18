create extension if not exists pgcrypto;

create type public.user_role as enum ('owner', 'admin', 'manager', 'user');
create type public.record_status as enum ('active', 'inactive');
create type public.person_type as enum ('pf', 'pj');
create type public.item_type as enum ('product', 'service');
create type public.stock_movement_type as enum ('entry', 'exit', 'adjustment');
create type public.payment_method as enum ('cash', 'credit_card', 'debit_card', 'pix', 'boleto', 'transfer', 'other');
create type public.service_order_status as enum ('draft', 'open', 'in_progress', 'completed', 'cancelled');
create type public.sale_status as enum ('open', 'completed', 'cancelled');
create type public.quote_status as enum ('draft', 'sent', 'approved', 'rejected', 'expired');
create type public.purchase_status as enum ('draft', 'ordered', 'received', 'cancelled');
create type public.warranty_status as enum ('active', 'claimed', 'expired', 'voided');
create type public.financial_type as enum ('receivable', 'payable');
create type public.financial_status as enum ('pending', 'paid', 'overdue', 'cancelled');
create type public.cash_status as enum ('open', 'closed');
create type public.cash_transaction_type as enum ('sale', 'payment', 'withdrawal', 'deposit', 'adjustment');

create table public.profiles (
  id uuid primary key references auth.users(id) on delete cascade,
  name text not null default '',
  is_super_admin boolean not null default false,
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now()
);

create table public.companies (
  id uuid primary key default gen_random_uuid(),
  name text not null,
  trade_name text,
  slug text not null unique,
  document text,
  email text,
  phone text,
  plan text not null default 'basic',
  status record_status not null default 'active',
  logo_path text,
  timezone text not null default 'America/Sao_Paulo',
  locale text not null default 'pt-BR',
  currency text not null default 'BRL',
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now()
);

create table public.branches (
  id uuid primary key default gen_random_uuid(),
  company_id uuid not null references public.companies(id) on delete cascade,
  name text not null, code text, document text, email text, phone text,
  postal_code text, street text, number text, complement text,
  neighborhood text, city text, state text,
  is_main boolean not null default false,
  status record_status not null default 'active',
  created_at timestamptz not null default now(), updated_at timestamptz not null default now(),
  unique(company_id, code)
);

create table public.company_settings (
  id uuid primary key default gen_random_uuid(),
  company_id uuid not null references public.companies(id) on delete cascade,
  setting_key text not null, setting_value text, setting_type text not null default 'string',
  created_at timestamptz not null default now(), updated_at timestamptz not null default now(),
  unique(company_id, setting_key)
);

create table public.company_users (
  id uuid primary key default gen_random_uuid(),
  company_id uuid not null references public.companies(id) on delete cascade,
  user_id uuid not null references auth.users(id) on delete cascade,
  role user_role not null default 'user', is_active boolean not null default true,
  created_at timestamptz not null default now(), updated_at timestamptz not null default now(),
  unique(company_id, user_id)
);

create table public.branch_users (
  id uuid primary key default gen_random_uuid(),
  branch_id uuid not null references public.branches(id) on delete cascade,
  user_id uuid not null references auth.users(id) on delete cascade,
  is_active boolean not null default true,
  created_at timestamptz not null default now(), updated_at timestamptz not null default now(),
  unique(branch_id, user_id)
);

create table public.customers (
  id uuid primary key default gen_random_uuid(), company_id uuid not null references public.companies(id) on delete cascade,
  type person_type not null default 'pf', name text not null, trade_name text, document text,
  state_registration text, phone text, whatsapp text, email text, postal_code text, street text,
  number text, complement text, neighborhood text, city text, state text, notes text,
  status record_status not null default 'active', created_at timestamptz not null default now(), updated_at timestamptz not null default now(),
  unique(company_id, document)
);

create table public.vehicle_brands (
  id uuid primary key default gen_random_uuid(), name text not null unique, external_code text unique,
  source text not null default 'fipe', is_active boolean not null default true,
  created_at timestamptz not null default now(), updated_at timestamptz not null default now()
);

create table public.vehicle_models (
  id uuid primary key default gen_random_uuid(), vehicle_brand_id uuid not null references public.vehicle_brands(id) on delete cascade,
  name text not null, external_code text, source text not null default 'fipe', is_active boolean not null default true,
  created_at timestamptz not null default now(), updated_at timestamptz not null default now(),
  unique(vehicle_brand_id, name), unique(vehicle_brand_id, external_code)
);

create table public.vehicles (
  id uuid primary key default gen_random_uuid(), company_id uuid not null references public.companies(id) on delete cascade,
  customer_id uuid not null references public.customers(id) on delete cascade,
  vehicle_brand_id uuid references public.vehicle_brands(id) on delete restrict,
  vehicle_model_id uuid references public.vehicle_models(id) on delete restrict,
  plate text not null, version text, year_manufacture text, year_model text, color text, chassis text,
  odometer integer check (odometer is null or odometer >= 0), notes text, status record_status not null default 'active',
  created_at timestamptz not null default now(), updated_at timestamptz not null default now(), unique(company_id, plate)
);

create table public.products (
  id uuid primary key default gen_random_uuid(), company_id uuid not null references public.companies(id) on delete cascade,
  type item_type not null, name text not null, sku text, barcode text, description text, unit text not null default 'UN',
  cost_price numeric(12,2) not null default 0 check (cost_price >= 0), sale_price numeric(12,2) not null default 0 check (sale_price >= 0),
  stock_quantity numeric(12,3) not null default 0, stock_minimum numeric(12,3) not null default 0,
  category text, brand text, ncm text, status record_status not null default 'active',
  created_at timestamptz not null default now(), updated_at timestamptz not null default now(), unique(company_id, sku)
);

create table public.suppliers (
  id uuid primary key default gen_random_uuid(), company_id uuid not null references public.companies(id) on delete cascade,
  type person_type not null default 'pj', name text not null, trade_name text, document text, state_registration text,
  phone text, whatsapp text, email text, contact_name text, postal_code text, street text, number text, complement text,
  neighborhood text, city text, state text, notes text, status record_status not null default 'active',
  created_at timestamptz not null default now(), updated_at timestamptz not null default now(), unique(company_id, document)
);

create table public.document_counters (
  company_id uuid not null references public.companies(id) on delete cascade,
  document_type text not null, last_number bigint not null default 0,
  primary key(company_id, document_type)
);

create table public.service_orders (
  id uuid primary key default gen_random_uuid(), company_id uuid not null references public.companies(id) on delete cascade,
  branch_id uuid references public.branches(id) on delete set null, customer_id uuid not null references public.customers(id) on delete restrict,
  vehicle_id uuid references public.vehicles(id) on delete set null, number bigint not null,
  status service_order_status not null default 'draft', complaint text, diagnosis text, internal_notes text,
  discount numeric(12,2) not null default 0, total numeric(12,2) not null default 0,
  opened_at timestamptz, completed_at timestamptz, created_at timestamptz not null default now(), updated_at timestamptz not null default now(),
  unique(company_id, number)
);

create table public.service_order_items (
  id uuid primary key default gen_random_uuid(), service_order_id uuid not null references public.service_orders(id) on delete cascade,
  product_id uuid references public.products(id) on delete set null, type item_type not null, description text not null,
  quantity numeric(12,3) not null default 1 check(quantity > 0), unit_price numeric(12,2) not null default 0,
  discount numeric(12,2) not null default 0, total numeric(12,2) not null default 0,
  created_at timestamptz not null default now(), updated_at timestamptz not null default now()
);

create table public.stock_movements (
  id uuid primary key default gen_random_uuid(), company_id uuid not null references public.companies(id) on delete cascade,
  product_id uuid not null references public.products(id) on delete restrict, type stock_movement_type not null,
  quantity numeric(12,3) not null check(quantity <> 0), unit_cost numeric(12,2), reason text,
  reference_type text, reference_id uuid, user_id uuid references auth.users(id) on delete set null,
  created_at timestamptz not null default now(), updated_at timestamptz not null default now()
);

create table public.sales (
  id uuid primary key default gen_random_uuid(), company_id uuid not null references public.companies(id) on delete cascade,
  branch_id uuid references public.branches(id) on delete set null, customer_id uuid references public.customers(id) on delete set null,
  user_id uuid references auth.users(id) on delete set null, number bigint not null, status sale_status not null default 'open',
  payment_method payment_method, subtotal numeric(12,2) not null default 0, discount numeric(12,2) not null default 0,
  total numeric(12,2) not null default 0, notes text, created_at timestamptz not null default now(), updated_at timestamptz not null default now(),
  unique(company_id, number)
);

create table public.sale_items (
  id uuid primary key default gen_random_uuid(), sale_id uuid not null references public.sales(id) on delete cascade,
  product_id uuid references public.products(id) on delete set null, description text not null,
  quantity numeric(12,3) not null default 1 check(quantity > 0), unit_price numeric(12,2) not null default 0,
  discount numeric(12,2) not null default 0, total numeric(12,2) not null default 0,
  created_at timestamptz not null default now(), updated_at timestamptz not null default now()
);

create table public.quotes (
  id uuid primary key default gen_random_uuid(), company_id uuid not null references public.companies(id) on delete cascade,
  customer_id uuid references public.customers(id) on delete set null, vehicle_id uuid references public.vehicles(id) on delete set null,
  number bigint not null, status quote_status not null default 'draft', valid_until date,
  subtotal numeric(12,2) not null default 0, discount numeric(12,2) not null default 0, total numeric(12,2) not null default 0,
  notes text, created_at timestamptz not null default now(), updated_at timestamptz not null default now(), unique(company_id, number)
);

create table public.quote_items (
  id uuid primary key default gen_random_uuid(), quote_id uuid not null references public.quotes(id) on delete cascade,
  product_id uuid references public.products(id) on delete set null, type item_type not null, description text not null,
  quantity numeric(12,3) not null default 1 check(quantity > 0), unit_price numeric(12,2) not null default 0,
  discount numeric(12,2) not null default 0, total numeric(12,2) not null default 0,
  created_at timestamptz not null default now(), updated_at timestamptz not null default now()
);

create table public.warranties (
  id uuid primary key default gen_random_uuid(), company_id uuid not null references public.companies(id) on delete cascade,
  customer_id uuid not null references public.customers(id) on delete restrict, vehicle_id uuid references public.vehicles(id) on delete set null,
  service_order_id uuid references public.service_orders(id) on delete set null, number bigint not null,
  description text, status warranty_status not null default 'active', start_date date, end_date date, terms text, claim_notes text,
  created_at timestamptz not null default now(), updated_at timestamptz not null default now(), unique(company_id, number)
);

create table public.purchases (
  id uuid primary key default gen_random_uuid(), company_id uuid not null references public.companies(id) on delete cascade,
  supplier_id uuid references public.suppliers(id) on delete set null, number bigint not null, invoice_number text,
  status purchase_status not null default 'draft', subtotal numeric(12,2) not null default 0,
  discount numeric(12,2) not null default 0, shipping numeric(12,2) not null default 0, total numeric(12,2) not null default 0,
  expected_date date, received_date date, notes text, created_at timestamptz not null default now(), updated_at timestamptz not null default now(),
  unique(company_id, number)
);

create table public.purchase_items (
  id uuid primary key default gen_random_uuid(), purchase_id uuid not null references public.purchases(id) on delete cascade,
  product_id uuid references public.products(id) on delete set null, description text not null,
  quantity numeric(12,3) not null default 1 check(quantity > 0), unit_cost numeric(12,2) not null default 0,
  total numeric(12,2) not null default 0, created_at timestamptz not null default now(), updated_at timestamptz not null default now()
);

create table public.financial_entries (
  id uuid primary key default gen_random_uuid(), company_id uuid not null references public.companies(id) on delete cascade,
  type financial_type not null, status financial_status not null default 'pending', description text not null, category text,
  amount numeric(12,2) not null check(amount >= 0), paid_amount numeric(12,2), due_date date, paid_date date,
  payment_method payment_method, reference_type text, reference_id uuid,
  customer_id uuid references public.customers(id) on delete set null, supplier_id uuid references public.suppliers(id) on delete set null,
  notes text, created_at timestamptz not null default now(), updated_at timestamptz not null default now()
);

create table public.cash_registers (
  id uuid primary key default gen_random_uuid(), company_id uuid not null references public.companies(id) on delete cascade,
  branch_id uuid references public.branches(id) on delete set null, user_id uuid not null references auth.users(id) on delete restrict,
  opening_balance numeric(12,2) not null default 0, closing_balance numeric(12,2), status cash_status not null default 'open',
  opened_at timestamptz not null default now(), closed_at timestamptz, notes text,
  created_at timestamptz not null default now(), updated_at timestamptz not null default now()
);

create unique index one_open_cash_register_per_user on public.cash_registers(company_id, user_id) where status = 'open';

create table public.cash_register_transactions (
  id uuid primary key default gen_random_uuid(), cash_register_id uuid not null references public.cash_registers(id) on delete cascade,
  type cash_transaction_type not null, description text not null, amount numeric(12,2) not null check(amount >= 0),
  payment_method payment_method not null default 'cash', reference_type text, reference_id uuid,
  created_at timestamptz not null default now(), updated_at timestamptz not null default now()
);

create or replace function public.touch_updated_at() returns trigger language plpgsql as $$
begin new.updated_at = now(); return new; end $$;

do $$ declare t text; begin
  foreach t in array array['profiles','companies','branches','company_settings','company_users','branch_users','customers','vehicle_brands','vehicle_models','vehicles','products','suppliers','service_orders','service_order_items','stock_movements','sales','sale_items','quotes','quote_items','warranties','purchases','purchase_items','financial_entries','cash_registers','cash_register_transactions']
  loop execute format('create trigger touch_updated_at before update on public.%I for each row execute function public.touch_updated_at()', t); end loop;
end $$;

create or replace function public.handle_new_user() returns trigger language plpgsql security definer set search_path = '' as $$
begin insert into public.profiles(id, name) values(new.id, coalesce(new.raw_user_meta_data ->> 'name', split_part(coalesce(new.email,''), '@', 1))); return new; end $$;
create trigger on_auth_user_created after insert on auth.users for each row execute function public.handle_new_user();

create or replace function public.is_company_member(target_company uuid) returns boolean
language sql stable security definer set search_path = '' as $$
  select exists(select 1 from public.company_users cu where cu.company_id = target_company and cu.user_id = auth.uid() and cu.is_active)
$$;

create or replace function public.has_company_role(target_company uuid, allowed_roles user_role[]) returns boolean
language sql stable security definer set search_path = '' as $$
  select exists(select 1 from public.company_users cu where cu.company_id = target_company and cu.user_id = auth.uid() and cu.is_active and cu.role = any(allowed_roles))
$$;

create or replace function public.next_document_number(target_company uuid, target_type text) returns bigint
language plpgsql security definer set search_path = '' as $$
declare result bigint;
begin
  if not public.is_company_member(target_company) then raise exception 'Access denied'; end if;
  insert into public.document_counters(company_id, document_type, last_number) values(target_company, target_type, 1)
  on conflict(company_id, document_type) do update set last_number = public.document_counters.last_number + 1
  returning last_number into result;
  return result;
end $$;

create or replace function public.register_stock_movement(target_company uuid, target_product uuid, movement_type stock_movement_type, movement_quantity numeric, movement_reason text default null)
returns uuid language plpgsql security definer set search_path = '' as $$
declare movement_id uuid; new_quantity numeric;
begin
  if not public.is_company_member(target_company) then raise exception 'Access denied'; end if;
  if movement_quantity <= 0 then raise exception 'Quantity must be positive'; end if;
  select case when movement_type = 'entry' then stock_quantity + movement_quantity when movement_type = 'exit' then greatest(0, stock_quantity - movement_quantity) else movement_quantity end
  into new_quantity from public.products where id = target_product and company_id = target_company and type = 'product' for update;
  if not found then raise exception 'Product not found'; end if;
  update public.products set stock_quantity = new_quantity where id = target_product;
  insert into public.stock_movements(company_id, product_id, type, quantity, reason, user_id)
  values(target_company, target_product, movement_type, movement_quantity, movement_reason, auth.uid()) returning id into movement_id;
  return movement_id;
end $$;

alter table public.profiles enable row level security;
create policy "profiles_read_self" on public.profiles for select using(id = auth.uid());
create policy "profiles_update_self" on public.profiles for update using(id = auth.uid()) with check(id = auth.uid());

alter table public.companies enable row level security;
create policy "companies_member_read" on public.companies for select using(public.is_company_member(id));
create policy "companies_admin_update" on public.companies for update using(public.has_company_role(id, array['owner','admin']::user_role[]));

alter table public.company_users enable row level security;
create policy "company_users_member_read" on public.company_users for select using(public.is_company_member(company_id));
create policy "company_users_admin_write" on public.company_users for all using(public.has_company_role(company_id, array['owner','admin']::user_role[])) with check(public.has_company_role(company_id, array['owner','admin']::user_role[]));

do $$ declare t text; begin
  foreach t in array array['branches','company_settings','customers','vehicles','products','suppliers','service_orders','stock_movements','sales','quotes','warranties','purchases','financial_entries','cash_registers','document_counters']
  loop
    execute format('alter table public.%I enable row level security', t);
    execute format('create policy %I on public.%I for select using(public.is_company_member(company_id))', t || '_member_read', t);
    execute format('create policy %I on public.%I for insert with check(public.is_company_member(company_id))', t || '_member_insert', t);
    execute format('create policy %I on public.%I for update using(public.is_company_member(company_id)) with check(public.is_company_member(company_id))', t || '_member_update', t);
    execute format('create policy %I on public.%I for delete using(public.has_company_role(company_id, array[''owner'',''admin'']::user_role[]))', t || '_admin_delete', t);
  end loop;
end $$;

alter table public.branch_users enable row level security;
create policy "branch_users_member_read" on public.branch_users for select using(exists(select 1 from public.branches b where b.id = branch_id and public.is_company_member(b.company_id)));
create policy "branch_users_admin_write" on public.branch_users for all using(exists(select 1 from public.branches b where b.id = branch_id and public.has_company_role(b.company_id, array['owner','admin']::user_role[]))) with check(exists(select 1 from public.branches b where b.id = branch_id and public.has_company_role(b.company_id, array['owner','admin']::user_role[])));

alter table public.vehicle_brands enable row level security;
alter table public.vehicle_models enable row level security;
create policy "vehicle_brands_authenticated_read" on public.vehicle_brands for select to authenticated using(true);
create policy "vehicle_models_authenticated_read" on public.vehicle_models for select to authenticated using(true);

do $$ declare child text; parent text; fk text; begin
  for child,parent,fk in values
    ('service_order_items','service_orders','service_order_id'), ('sale_items','sales','sale_id'),
    ('quote_items','quotes','quote_id'), ('purchase_items','purchases','purchase_id'),
    ('cash_register_transactions','cash_registers','cash_register_id')
  loop
    execute format('alter table public.%I enable row level security', child);
    execute format('create policy %I on public.%I for select using(exists(select 1 from public.%I p where p.id = %I and public.is_company_member(p.company_id)))', child || '_member_read', child, parent, fk);
    execute format('create policy %I on public.%I for insert with check(exists(select 1 from public.%I p where p.id = %I and public.is_company_member(p.company_id)))', child || '_member_insert', child, parent, fk);
    execute format('create policy %I on public.%I for update using(exists(select 1 from public.%I p where p.id = %I and public.is_company_member(p.company_id)))', child || '_member_update', child, parent, fk);
    execute format('create policy %I on public.%I for delete using(exists(select 1 from public.%I p where p.id = %I and public.has_company_role(p.company_id, array[''owner'',''admin'']::user_role[])))', child || '_admin_delete', child, parent, fk);
  end loop;
end $$;

grant execute on function public.next_document_number(uuid, text) to authenticated;
grant execute on function public.register_stock_movement(uuid, uuid, stock_movement_type, numeric, text) to authenticated;
