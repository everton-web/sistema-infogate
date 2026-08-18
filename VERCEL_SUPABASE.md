# Deploy — Vercel e Supabase

## Desenvolvimento local

1. Instale Node.js LTS e, caso use o Supabase local, Docker Desktop.
2. Copie `.env.example` para `.env.local`.
3. Execute `npm install` e `npm run dev`.
4. Para a stack Supabase local, execute `npm run db:start`.

As alterações de banco ficam em `supabase/migrations`. Para recriar somente o banco local, use `npm run db:reset`.

## Supabase vinculado

```powershell
npx supabase login
npx supabase link --project-ref hezwhdaijmbzgyocdita
npx supabase db push --dry-run
```

Revise o dry-run antes de aplicar qualquer migration. Nunca execute `db reset --linked` no projeto hospedado.

## Vercel

O projeto deve ser importado com a **raiz do repositório** como Root Directory. Não configure uma subpasta.

Variáveis necessárias:

- `NEXT_PUBLIC_SUPABASE_URL`
- `NEXT_PUBLIC_SUPABASE_PUBLISHABLE_KEY`
- `SUPABASE_SECRET_KEY` — somente servidor
- `NEXT_PUBLIC_APP_URL`

O merge na branch `main` dispara o deploy de produção. Pull Requests devem passar pelo workflow `Next.js CI` antes do merge.

## Segurança

- Não versionar `.env.local`.
- Não expor `SUPABASE_SECRET_KEY` em componentes client-side.
- Manter as URLs de produção e preview autorizadas no Supabase Auth.
- Criar toda alteração de banco como migration revisável.
