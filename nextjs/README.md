# Sistema InfoGate

Aplicação de gestão comercial da Canal Som, construída com Next.js, TypeScript e Supabase e publicada pela Vercel.

## Ambiente local

```powershell
npm install
Copy-Item .env.example .env.local
npm run dev
```

O sistema fica disponível em [http://localhost:3000](http://localhost:3000).

## Validação obrigatória

```powershell
npm run typecheck
npm run lint
npm run build
```

## Estrutura principal

- `src/app`: páginas, layouts, ações e rotas da aplicação.
- `src/components`: componentes reutilizáveis e gerenciadores dos módulos.
- `src/lib`: autenticação, contexto da empresa e clientes Supabase.
- `supabase/migrations`: histórico revisável da estrutura do banco.
- `public`: imagens e recursos visuais públicos.

## Colaboradores

Leia o [guia completo de onboarding](./ONBOARDING_COLABORADOR.md) antes da primeira alteração. Ele inclui GitHub, Supabase, ambiente local, Codex, Pull Requests e deploy.

## Produção

- Repositório: [everton-web/sistema-infogate](https://github.com/everton-web/sistema-infogate)
- Aplicação: [sistema-infogate.vercel.app](https://sistema-infogate.vercel.app)

Alterações entram por Pull Request. O merge na branch `main` dispara o deploy de produção.
