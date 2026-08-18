# Onboarding de desenvolvimento — Sistema InfoGate

Este guia prepara um novo colaborador para desenvolver o Sistema InfoGate com GitHub, Next.js, Supabase, Vercel e Codex.

## 1. Acessos que o responsável pelo projeto deve conceder

Antes de o colaborador começar, o responsável pelo projeto deve:

1. Adicioná-lo como colaborador do repositório `everton-web/sistema-infogate` no GitHub.
2. Se ele for trabalhar com banco, Auth, Storage ou migrations, convidá-lo para o projeto Supabase com o perfil **Developer**.
3. Não compartilhar senhas pessoais, tokens de acesso, chaves de conta ou arquivos `.env.local` por mensagem comum.
4. Entregar os valores necessários para o ambiente local por um canal seguro, preferencialmente um gerenciador de senhas.

O colaborador não precisa acessar a Vercel para desenvolver. Pull Requests e merges no GitHub já disparam os deploys configurados. O acesso à Vercel só é necessário para quem administra variáveis, domínios ou logs de produção.

## 2. Instalar as ferramentas no Windows

Abra o PowerShell e instale as ferramentas básicas:

```powershell
winget install --id Git.Git
winget install --id OpenJS.NodeJS.LTS
winget install --id GitHub.cli
winget install --id 9PLM9XGG6VKS -s msstore
```

O último comando instala o aplicativo ChatGPT/Codex para Windows. Depois das instalações, feche e abra novamente o terminal.

Confirme o ambiente:

```powershell
git --version
node --version
npm --version
gh --version
```

Documentação oficial: [Codex no Windows](https://learn.chatgpt.com/docs/windows/windows-app) e [início rápido do Codex](https://learn.chatgpt.com/docs/quickstart?setup=app).

## 3. Entrar no GitHub

O colaborador deve aceitar o convite recebido do GitHub e depois autenticar o GitHub CLI:

```powershell
gh auth login
```

Escolha `GitHub.com`, `HTTPS` e a autenticação pelo navegador. Confirme:

```powershell
gh auth status
```

## 4. Clonar o projeto

É recomendável usar uma pasta local fora do OneDrive para evitar conflitos de sincronização:

```powershell
New-Item -ItemType Directory -Force C:\Projetos
Set-Location C:\Projetos
git clone https://github.com/everton-web/sistema-infogate.git
Set-Location .\sistema-infogate\nextjs
```

O sistema atual fica na pasta `nextjs`. A aplicação Laravel presente no repositório é apenas referência da versão anterior e não deve receber novas funcionalidades.

## 5. Instalar as dependências

Dentro da pasta `nextjs`:

```powershell
npm install
```

## 6. Configurar o ambiente local

Crie o arquivo local a partir do exemplo:

```powershell
Copy-Item .env.example .env.local
```

Preencha `.env.local` com os valores fornecidos pelo responsável:

```ini
NEXT_PUBLIC_SUPABASE_URL=
NEXT_PUBLIC_SUPABASE_PUBLISHABLE_KEY=
SUPABASE_SECRET_KEY=
NEXT_PUBLIC_APP_URL=http://localhost:3000
```

Regras de segurança:

- Nunca fazer commit de `.env.local`.
- Nunca colocar `SUPABASE_SECRET_KEY` em código executado no navegador.
- Nunca colar chaves ou senhas em prompts do Codex, Issues ou Pull Requests.
- Usar a chave secreta somente em rotas administrativas executadas no servidor.

## 7. Executar e validar o sistema

Inicie o servidor local:

```powershell
npm run dev
```

Abra [http://localhost:3000](http://localhost:3000). Antes de enviar qualquer alteração, rode:

```powershell
npm run typecheck
npm run lint
npm run build
```

## 8. Configurar o Supabase CLI quando necessário

Esta etapa só é necessária para alterações no banco de dados:

```powershell
npx supabase login
npx supabase link --project-ref hezwhdaijmbzgyocdita
```

Toda mudança de estrutura deve ser criada como migration dentro de `supabase/migrations`. Não alterar tabelas de produção manualmente sem registrar a mesma mudança em uma migration revisável.

## 9. Abrir o projeto no Codex

1. Abra o aplicativo ChatGPT no Windows e entre com a conta do próprio colaborador.
2. Selecione **Codex** no seletor de modo.
3. Clique em **Add new project** ou pressione `Ctrl+O`.
4. Abra exatamente a pasta `C:\Projetos\sistema-infogate\nextjs`.
5. Mantenha o modo **Ask for approval** durante o onboarding. Evite Full Access.
6. Inicie uma nova tarefa e envie:

```text
Leia completamente o AGENTS.md e o README.md deste projeto.
Depois examine a estrutura sem alterar arquivos e me explique:
1. como executar o sistema;
2. onde ficam as páginas e componentes;
3. como o Supabase é acessado;
4. quais validações devem rodar antes de um Pull Request.
```

O Codex lê `AGENTS.md` automaticamente e usa as regras do repositório para orientar edições, testes e segurança. Consulte [instruções de projeto com AGENTS.md](https://learn.chatgpt.com/docs/agent-configuration/agents-md).

## 10. Fluxo de uma alteração com o Codex

Antes de começar uma tarefa:

```powershell
git switch main
git pull --ff-only
git switch -c feature/descricao-curta
```

Exemplo de solicitação ao Codex:

```text
Implemente [descrever a alteração].
Antes de editar, localize os arquivos envolvidos e explique rapidamente o impacto.
Preserve o isolamento multiempresa e a identidade visual Canal Som/InfoGate.
Depois, execute typecheck, lint e build, revise o diff e não faça push sem minha autorização.
```

Ao final, o colaborador deve revisar o diff apresentado pelo Codex. Se estiver correto:

```powershell
git status
git diff
git add caminho/dos/arquivos
git commit -m "feat: descrição objetiva"
git push -u origin feature/descricao-curta
gh pr create --fill
```

O Pull Request deve explicar o que mudou, por que mudou, como foi testado e se existe impacto no banco ou nas variáveis de ambiente.

## 11. Fluxo de revisão e deploy

```text
branch do colaborador
        ↓
Pull Request no GitHub
        ↓
revisão e correções
        ↓
merge na main
        ↓
deploy automático na Vercel
        ↓
validação em https://sistema-infogate.vercel.app
```

Não trabalhar diretamente na `main`. Não fazer merge com `typecheck`, `lint` ou `build` quebrados.

## 12. Checklist antes de abrir o Pull Request

- [ ] A alteração está em uma branch própria.
- [ ] Nenhum segredo, senha ou `.env.local` entrou no diff.
- [ ] O isolamento por `company_id` foi preservado.
- [ ] Mudanças no banco possuem migration.
- [ ] A identidade visual Canal Som/InfoGate foi preservada.
- [ ] `npm run typecheck` passou.
- [ ] `npm run lint` passou.
- [ ] `npm run build` passou.
- [ ] O fluxo alterado foi testado em `localhost:3000`.
- [ ] O Pull Request descreve impacto e validação.

## 13. Problemas comuns

### `npm.ps1` bloqueado pelo PowerShell

Use os executáveis `.cmd`:

```powershell
npm.cmd install
npm.cmd run dev
```

### Git não aparece no Codex

Confirme `git --version`, reinicie o aplicativo e abra novamente a pasta do projeto.

### Erro de autenticação do Supabase

Confira se `.env.local` existe, se as quatro variáveis estão preenchidas e reinicie `npm run dev`.

### A migration não conecta ao projeto

Execute novamente `npx supabase login` e `npx supabase link --project-ref hezwhdaijmbzgyocdita`. Se o acesso for negado, peça ao responsável o convite de Developer no Supabase.
