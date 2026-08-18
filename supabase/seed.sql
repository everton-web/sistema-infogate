-- O primeiro usuário deve ser criado pelo Supabase Studio ou por convite.
-- Depois, associe-o a uma empresa usando o painel ou o script de bootstrap documentado no README.
insert into public.vehicle_brands(name, external_code) values
  ('Chevrolet', '23'), ('Fiat', '21'), ('Ford', '22'), ('Honda', '25'),
  ('Hyundai', '26'), ('Jeep', '29'), ('Renault', '48'), ('Toyota', '56'),
  ('Volkswagen', '59')
on conflict do nothing;
