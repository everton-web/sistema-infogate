alter function public.touch_updated_at() set search_path = '';

create index stock_movements_company_id_idx
  on public.stock_movements(company_id);
create index stock_movements_user_id_idx
  on public.stock_movements(user_id);

drop policy "company_users_admin_write" on public.company_users;
create policy "company_users_admin_insert"
  on public.company_users for insert to authenticated
  with check(public.has_company_role(company_id, array['owner','admin']::public.user_role[]));
create policy "company_users_admin_update"
  on public.company_users for update to authenticated
  using(public.has_company_role(company_id, array['owner','admin']::public.user_role[]))
  with check(public.has_company_role(company_id, array['owner','admin']::public.user_role[]));
create policy "company_users_admin_delete"
  on public.company_users for delete to authenticated
  using(public.has_company_role(company_id, array['owner','admin']::public.user_role[]));

drop policy "branch_users_admin_write" on public.branch_users;
create policy "branch_users_admin_insert"
  on public.branch_users for insert to authenticated
  with check(exists(
    select 1 from public.branches b
    where b.id = branch_id
      and public.has_company_role(b.company_id, array['owner','admin']::public.user_role[])
  ));
create policy "branch_users_admin_update"
  on public.branch_users for update to authenticated
  using(exists(
    select 1 from public.branches b
    where b.id = branch_id
      and public.has_company_role(b.company_id, array['owner','admin']::public.user_role[])
  ))
  with check(exists(
    select 1 from public.branches b
    where b.id = branch_id
      and public.has_company_role(b.company_id, array['owner','admin']::public.user_role[])
  ));
create policy "branch_users_admin_delete"
  on public.branch_users for delete to authenticated
  using(exists(
    select 1 from public.branches b
    where b.id = branch_id
      and public.has_company_role(b.company_id, array['owner','admin']::public.user_role[])
  ));
