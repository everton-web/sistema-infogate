<!-- BEGIN:nextjs-agent-rules -->

# This is NOT the Next.js you know

This version has breaking changes — APIs, conventions, and file structure may all differ from your training data. Read the relevant guide in `node_modules/next/dist/docs/` (resolved from this file's directory; in monorepos the `next` package may not be visible from the repo root) before writing any code. Heed deprecation notices.

This block is written and re-added by `next dev` — verify at `node_modules/next/dist/server/lib/generate-agent-files.js`. Removing it from a diff only re-creates the uncommitted change; committing it with your work keeps the tree clean.

<!-- END:nextjs-agent-rules -->

# Sistema InfoGate — regras do projeto

## Escopo ativo

- A raiz deste repositório contém a aplicação final e ativa.
- Não recrie pastas ou dependências da implementação Laravel anterior.
- Preserve a identidade visual Canal Som/InfoGate e o idioma `pt-BR`.

## Segurança e dados

- Nunca leia, exiba, registre ou faça commit de `.env.local` ou de chaves secretas.
- `SUPABASE_SECRET_KEY` só pode ser usada em código executado no servidor.
- Preserve o isolamento multiempresa por `company_id` e as políticas RLS.
- Toda alteração de schema deve possuir uma migration em `supabase/migrations`.
- Não execute migrations remotas nem altere produção sem autorização explícita.

## Implementação

- Antes de editar, inspecione os arquivos envolvidos e preserve mudanças existentes do usuário.
- Prefira alterações pequenas, tipadas e compatíveis com os padrões já presentes.
- Não instale dependências de produção sem explicar a necessidade e obter autorização.
- Não faça commit, push, merge ou deploy sem solicitação explícita do usuário atual.

## Validação

Antes de considerar uma mudança concluída, execute:

```powershell
npm run typecheck
npm run lint
npm run build
```

Informe claramente qualquer teste que não pôde ser executado.

## Colaboração

- Trabalhe em branches `feature/*`, `fix/*` ou `docs/*`.
- Não desenvolva diretamente na branch `main`.
- Pull Requests devem explicar mudança, motivo, impacto e validação realizada.
- Consulte `ONBOARDING_COLABORADOR.md` para o fluxo completo.
