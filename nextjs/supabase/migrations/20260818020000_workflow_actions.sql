create or replace function public.pay_financial_entry(target_entry uuid, payment_amount numeric, payment_date date, method public.payment_method)
returns void language plpgsql security definer set search_path = '' as $$
declare target_company uuid;
begin
  select company_id into target_company from public.financial_entries where id=target_entry for update;
  if not public.is_company_member(target_company) then raise exception 'Access denied'; end if;
  if payment_amount <= 0 then raise exception 'Payment must be positive'; end if;
  update public.financial_entries set status='paid',paid_amount=payment_amount,paid_date=coalesce(payment_date,current_date),payment_method=method where id=target_entry;
end $$;

create or replace function public.close_cash_register(target_register uuid, balance numeric, closing_notes text default null)
returns void language plpgsql security definer set search_path = '' as $$
declare target_company uuid;
begin
  select company_id into target_company from public.cash_registers where id=target_register and status='open' for update;
  if target_company is null or not public.is_company_member(target_company) then raise exception 'Cash register not found'; end if;
  update public.cash_registers set status='closed',closing_balance=greatest(0,balance),closed_at=now(),notes=coalesce(closing_notes,notes) where id=target_register;
end $$;

create or replace function public.add_cash_transaction(target_register uuid, transaction_type public.cash_transaction_type, description_value text, amount_value numeric, method public.payment_method default 'cash')
returns uuid language plpgsql security definer set search_path = '' as $$
declare target_company uuid; result uuid;
begin
  select company_id into target_company from public.cash_registers where id=target_register and status='open';
  if target_company is null or not public.is_company_member(target_company) then raise exception 'Open cash register not found'; end if;
  if amount_value <= 0 then raise exception 'Amount must be positive'; end if;
  insert into public.cash_register_transactions(cash_register_id,type,description,amount,payment_method) values(target_register,transaction_type,description_value,amount_value,method) returning id into result;
  return result;
end $$;

create or replace function public.update_document_status(document_kind text, target_document uuid, new_status text)
returns void language plpgsql security definer set search_path = '' as $$
declare target_company uuid; previous_status text; line record;
begin
  if document_kind='service_order' then
    select company_id,status::text into target_company,previous_status from public.service_orders where id=target_document for update;
  elsif document_kind='purchase' then
    select company_id,status::text into target_company,previous_status from public.purchases where id=target_document for update;
  elsif document_kind='quote' then
    select company_id,status::text into target_company,previous_status from public.quotes where id=target_document for update;
  elsif document_kind='sale' then
    select company_id,status::text into target_company,previous_status from public.sales where id=target_document for update;
  else raise exception 'Invalid document type'; end if;
  if target_company is null or not public.is_company_member(target_company) then raise exception 'Document not found'; end if;
  if document_kind='service_order' then
    if new_status not in ('draft','open','in_progress','completed','cancelled') then raise exception 'Invalid status'; end if;
    update public.service_orders set status=new_status::public.service_order_status,completed_at=case when new_status='completed' then now() else completed_at end where id=target_document;
  elsif document_kind='purchase' then
    if new_status not in ('draft','ordered','received','cancelled') then raise exception 'Invalid status'; end if;
    update public.purchases set status=new_status::public.purchase_status,received_date=case when new_status='received' then current_date else received_date end where id=target_document;
    if new_status='received' and previous_status<>'received' then
      for line in select product_id,quantity,unit_cost from public.purchase_items where purchase_id=target_document and product_id is not null loop
        update public.products set stock_quantity=stock_quantity+line.quantity where id=line.product_id and company_id=target_company;
        insert into public.stock_movements(company_id,product_id,type,quantity,unit_cost,reason,reference_type,reference_id,user_id) values(target_company,line.product_id,'entry',line.quantity,line.unit_cost,'Recebimento de compra','purchase',target_document,auth.uid());
      end loop;
    end if;
  elsif document_kind='quote' then
    if new_status not in ('draft','sent','approved','rejected','expired') then raise exception 'Invalid status'; end if;
    update public.quotes set status=new_status::public.quote_status where id=target_document;
  else
    if new_status not in ('open','completed','cancelled') then raise exception 'Invalid status'; end if;
    update public.sales set status=new_status::public.sale_status where id=target_document;
  end if;
end $$;

grant execute on function public.pay_financial_entry(uuid,numeric,date,public.payment_method) to authenticated;
grant execute on function public.close_cash_register(uuid,numeric,text) to authenticated;
grant execute on function public.add_cash_transaction(uuid,public.cash_transaction_type,text,numeric,public.payment_method) to authenticated;
grant execute on function public.update_document_status(text,uuid,text) to authenticated;
