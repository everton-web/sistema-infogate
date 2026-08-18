create or replace function public.bootstrap_company(company_name text, company_slug text)
returns uuid language plpgsql security definer set search_path = '' as $$
declare result uuid;
begin
  if auth.uid() is null then raise exception 'Authentication required'; end if;
  if exists(select 1 from public.company_users where user_id=auth.uid()) then raise exception 'User already belongs to a company'; end if;
  if length(trim(company_name))<2 or company_slug !~ '^[a-z0-9]+(?:-[a-z0-9]+)*$' then raise exception 'Invalid company data'; end if;
  insert into public.companies(name,trade_name,slug) values(trim(company_name),trim(company_name),company_slug) returning id into result;
  insert into public.branches(company_id,name,code,is_main) values(result,'Matriz','MATRIZ',true);
  insert into public.company_users(company_id,user_id,role,is_active) values(result,auth.uid(),'owner',true);
  return result;
end $$;
revoke execute on function public.bootstrap_company(text,text) from public, anon, authenticated;
grant execute on function public.bootstrap_company(text,text) to authenticated;
