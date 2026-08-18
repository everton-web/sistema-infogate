create or replace function public.create_business_document(
  document_kind text, target_company uuid, header jsonb, items jsonb
) returns uuid language plpgsql security definer set search_path = '' as $$
declare
  document_id uuid;
  document_number bigint;
  item jsonb;
  item_total numeric(12,2);
  subtotal_value numeric(12,2) := 0;
  discount_value numeric(12,2) := greatest(0, coalesce((header->>'discount')::numeric, 0));
  shipping_value numeric(12,2) := greatest(0, coalesce((header->>'shipping')::numeric, 0));
  product_company uuid;
begin
  if not public.is_company_member(target_company) then raise exception 'Access denied'; end if;
  if jsonb_typeof(items) <> 'array' or jsonb_array_length(items) = 0 then raise exception 'At least one item is required'; end if;
  if document_kind not in ('service_order','sale','quote','purchase') then raise exception 'Invalid document type'; end if;
  for item in select * from jsonb_array_elements(items) loop
    if coalesce((item->>'quantity')::numeric,0) <= 0 then raise exception 'Item quantity must be positive'; end if;
    if coalesce((item->>case when document_kind='purchase' then 'unit_cost' else 'unit_price' end)::numeric,0) < 0 then raise exception 'Item price cannot be negative'; end if;
    if nullif(item->>'product_id','') is not null then
      select company_id into product_company from public.products where id=(item->>'product_id')::uuid;
      if product_company is distinct from target_company then raise exception 'Product does not belong to company'; end if;
    end if;
  end loop;
  document_number := public.next_document_number(target_company, document_kind);
  if document_kind = 'service_order' then
    if not exists(select 1 from public.customers where id=(header->>'customer_id')::uuid and company_id=target_company) then raise exception 'Invalid customer'; end if;
    insert into public.service_orders(company_id,customer_id,vehicle_id,number,status,complaint,diagnosis,internal_notes,discount,total,opened_at)
    values(target_company,(header->>'customer_id')::uuid,nullif(header->>'vehicle_id','')::uuid,document_number,'open',header->>'complaint',header->>'diagnosis',header->>'internal_notes',discount_value,0,now()) returning id into document_id;
    for item in select * from jsonb_array_elements(items) loop
      item_total:=greatest(0,(item->>'quantity')::numeric*(item->>'unit_price')::numeric-coalesce((item->>'discount')::numeric,0));subtotal_value:=subtotal_value+item_total;
      insert into public.service_order_items(service_order_id,product_id,type,description,quantity,unit_price,discount,total)
      values(document_id,nullif(item->>'product_id','')::uuid,coalesce(nullif(item->>'type',''),'service')::public.item_type,item->>'description',(item->>'quantity')::numeric,(item->>'unit_price')::numeric,coalesce((item->>'discount')::numeric,0),item_total);
    end loop;
    update public.service_orders set total=greatest(0,subtotal_value-discount_value) where id=document_id;
  elsif document_kind = 'sale' then
    insert into public.sales(company_id,customer_id,user_id,number,status,payment_method,subtotal,discount,total,notes)
    values(target_company,nullif(header->>'customer_id','')::uuid,auth.uid(),document_number,'completed',nullif(header->>'payment_method','')::public.payment_method,0,discount_value,0,header->>'notes') returning id into document_id;
    for item in select * from jsonb_array_elements(items) loop
      item_total:=greatest(0,(item->>'quantity')::numeric*(item->>'unit_price')::numeric-coalesce((item->>'discount')::numeric,0));subtotal_value:=subtotal_value+item_total;
      insert into public.sale_items(sale_id,product_id,description,quantity,unit_price,discount,total)
      values(document_id,nullif(item->>'product_id','')::uuid,item->>'description',(item->>'quantity')::numeric,(item->>'unit_price')::numeric,coalesce((item->>'discount')::numeric,0),item_total);
    end loop;
    update public.sales set subtotal=subtotal_value,total=greatest(0,subtotal_value-discount_value) where id=document_id;
  elsif document_kind = 'quote' then
    insert into public.quotes(company_id,customer_id,vehicle_id,number,status,valid_until,subtotal,discount,total,notes)
    values(target_company,nullif(header->>'customer_id','')::uuid,nullif(header->>'vehicle_id','')::uuid,document_number,'draft',nullif(header->>'valid_until','')::date,0,discount_value,0,header->>'notes') returning id into document_id;
    for item in select * from jsonb_array_elements(items) loop
      item_total:=greatest(0,(item->>'quantity')::numeric*(item->>'unit_price')::numeric-coalesce((item->>'discount')::numeric,0));subtotal_value:=subtotal_value+item_total;
      insert into public.quote_items(quote_id,product_id,type,description,quantity,unit_price,discount,total)
      values(document_id,nullif(item->>'product_id','')::uuid,coalesce(nullif(item->>'type',''),'service')::public.item_type,item->>'description',(item->>'quantity')::numeric,(item->>'unit_price')::numeric,coalesce((item->>'discount')::numeric,0),item_total);
    end loop;
    update public.quotes set subtotal=subtotal_value,total=greatest(0,subtotal_value-discount_value) where id=document_id;
  else
    insert into public.purchases(company_id,supplier_id,number,invoice_number,status,subtotal,discount,shipping,total,expected_date,notes)
    values(target_company,nullif(header->>'supplier_id','')::uuid,document_number,header->>'invoice_number','draft',0,discount_value,shipping_value,0,nullif(header->>'expected_date','')::date,header->>'notes') returning id into document_id;
    for item in select * from jsonb_array_elements(items) loop
      item_total:=greatest(0,(item->>'quantity')::numeric*(item->>'unit_cost')::numeric);subtotal_value:=subtotal_value+item_total;
      insert into public.purchase_items(purchase_id,product_id,description,quantity,unit_cost,total)
      values(document_id,nullif(item->>'product_id','')::uuid,item->>'description',(item->>'quantity')::numeric,(item->>'unit_cost')::numeric,item_total);
    end loop;
    update public.purchases set subtotal=subtotal_value,total=greatest(0,subtotal_value-discount_value+shipping_value) where id=document_id;
  end if;
  return document_id;
end $$;
grant execute on function public.create_business_document(text,uuid,jsonb,jsonb) to authenticated;
