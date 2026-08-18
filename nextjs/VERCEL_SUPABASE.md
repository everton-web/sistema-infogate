# Implantação Next.js + Supabase

## Desenvolvimento local

1. Instale Node.js 20.9+ e Docker Desktop.
2. Copie `.env.example` para `.env.local`.
3. Execute `npm install`, `npm run db:start` e `npm run dev`.
4. O Supabase Studio local fica em `http://127.0.0.1:54323`.

As alterações de banco ficam em `supabase/migrations`. Para recriar o banco local, use `npm run db:reset`.

## Primeiro acesso

Crie o primeiro usuário no Supabase Studio (local) ou em Authentication > Users (nuvem). Entre no sistema e execute no cliente autenticado a função `bootstrap_company(nome, slug)`, ou associe o usuário manualmente nas tabelas `companies` e `company_users` usando o painel.

## Supabase de desenvolvimento

1. Crie um projeto separado para desenvolvimento.
2. Execute `npx supabase login` e `npx supabase link --project-ref SEU_REF`.
3. Confira com `npx supabase db push --dry-run`.
4. Aplique com `npx supabase db push`.
5. Nunca execute `db reset --linked` em produção.

## Vercel

Importe o repositório na Vercel e configure `nextjs` como Root Directory. Cadastre:

- `NEXT_PUBLIC_SUPABASE_URL`
- `NEXT_PUBLIC_SUPABASE_PUBLISHABLE_KEY`
- `SUPABASE_SECRET_KEY` (somente servidor)
- `NEXT_PUBLIC_APP_URL`

Use projetos Supabase distintos para Preview/Development e Production. Cadastre as URLs da Vercel nas redirect URLs do Supabase Auth.

## Migração dos dados SQLite

Faça backup antes da migração. Com o Laravel fora de escrita:

```bash
php nextjs/scripts/export-legacy.php database/database.sqlite nextjs/legacy-export.json
cd nextjs
node scripts/import-legacy.mjs legacy-export.json
```

O importador cria UUIDs, preserva relacionamentos e recria os usuários. Senhas Laravel não são copiadas: cada usuário precisa receber convite ou redefinir a senha. O arquivo `legacy-export.json` contém dados pessoais e não pode ser commitado.
