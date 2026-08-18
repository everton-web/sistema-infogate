# Checklist - Migracao InfoGate para Next.js + Supabase + Vercel

## FASE 1: Setup Inicial

### Supabase
- [ ] Criar conta no Supabase (supabase.com)
- [ ] Criar projeto "infogate-gestao"
- [ ] Copiar URL do projeto e chaves (anon key, service role key)
- [ ] Executar SQL do schema (arquivo MIGRACAO_NEXTJS_SUPABASE.md)
- [ ] Configurar Auth (email/password)
- [ ] Criar usuario admin inicial
- [ ] Configurar RLS em todas as tabelas com company_id

### Next.js
- [ ] Criar projeto: `npx create-next-app@latest infogate-nextjs --typescript --tailwind --app`
- [ ] Instalar dependencias: `@supabase/supabase-js`, `@supabase/ssr`
- [ ] Configurar variáveis de ambiente (.env.local)
- [ ] Criar clients Supabase (browser e server)
- [ ] Configurar middleware de autenticacao

### Vercel
- [ ] Criar conta no Vercel (vercel.com)
- [ ] Conectar repositorio GitHub
- [ ] Configurar variaveis de ambiente no Vercel
- [ ] Primeiro deploy (hello world)

### GitHub
- [ ] Criar repositorio (ou usar existente)
- [ ] Push do projeto Next.js
- [ ] Verificar deploy automatico no Vercel

---

## FASE 2: Infraestrutura

### Autenticacao
- [ ] Pagina de login (`/login`)
- [ ] Supabase Auth signInWithPassword
- [ ] Supabase Auth signOut
- [ ] Middleware Next.js (proteger rotas autenticadas)
- [ ] Redirect automatico para /login quando nao autenticado

### Multi-tenancy
- [ ] AuthProvider (React Context com usuario logado)
- [ ] CompanyProvider (React Context com empresa/filial ativa)
- [ ] Selecao de empresa ativa (para usuarios multi-empresa)
- [ ] RLS policies em todas as tabelas (filtro por company_id)
- [ ] Testar isolamento entre empresas

### Layout
- [ ] DashboardLayout (sidebar + header + content area)
- [ ] Sidebar com menu de navegacao
- [ ] Header com info do usuario e empresa
- [ ] Componente de notificacoes/flash messages
- [ ] Responsividade mobile

### Componentes Base
- [ ] DataTable (listagem com paginacao, busca, ordenacao)
- [ ] Form components (Input, Select, Textarea, etc.)
- [ ] Modal/Dialog
- [ ] Botoes (primario, secundario, perigo)
- [ ] Cards e paineis
- [ ] Loading/Skeleton states
- [ ] Toast/Alert notifications

---

## FASE 3: Modulos de Cadastro

### Dashboard
- [ ] Pagina principal com resumos
- [ ] Cards de totais (clientes, veiculos, OS abertas, etc.)
- [ ] Graficos basicos (vendas, financeiro)

### Clientes (4 paginas)
- [ ] Index: listagem com busca e filtros
- [ ] Create: formulario de cadastro (PF/PJ)
- [ ] Show: visualizacao com veiculos vinculados
- [ ] Edit: edicao do cadastro
- [ ] API: GET /api/customers, POST, PUT
- [ ] Validacoes de documento (CPF/CNPJ)

### Veiculos (2 paginas)
- [ ] Index: listagem com busca
- [ ] Create: formulario (com selecao de marca/modelo)
- [ ] API: GET /api/vehicles, POST
- [ ] Endpoint: GET /api/vehicles/models?brand_id=X (modelos por marca)
- [ ] Seed de marcas/modelos (FIPE)

### Produtos (4 paginas)
- [ ] Index: listagem com filtros (produto/servico)
- [ ] Create: formulario de cadastro
- [ ] Show: visualizacao com estoque
- [ ] Edit: edicao do cadastro
- [ ] API: GET /api/products, POST, PUT

### Fornecedores (4 paginas)
- [ ] Index: listagem com busca
- [ ] Create: formulario de cadastro
- [ ] Show: visualizacao com compras vinculadas
- [ ] Edit: edicao do cadastro
- [ ] API: GET /api/suppliers, POST, PUT

---

## FASE 4: Modulos Operacionais

### Ordens de Servico (3 paginas)
- [ ] Index: listagem com filtros de status
- [ ] Create: formulario com selecao de cliente, veiculo, itens
- [ ] Show: visualizacao detalhada com itens
- [ ] API: CRUD + endpoint customerVehicles
- [ ] Logica de calculo de totais
- [ ] Transicao de status (draft > open > in_progress > completed)

### Estoque (2 paginas)
- [ ] Index: listagem de produtos com estoque
- [ ] Movements: historico de movimentacoes por produto
- [ ] API: GET /api/stock, POST /api/stock (entrada/saida/ajuste)
- [ ] Atualizacao automatica de stock_quantity no produto

### Vendas (3 paginas)
- [ ] Index: listagem com filtros
- [ ] Create: formulario PDV (selecao rapida de itens)
- [ ] Show: visualizacao detalhada
- [ ] API: CRUD
- [ ] Baixa automatica de estoque ao completar venda
- [ ] Geracao automatica de lancamento financeiro

### Orcamentos (3 paginas)
- [ ] Index: listagem com filtros de status
- [ ] Create: formulario com itens
- [ ] Show: visualizacao (com opcao de converter em OS/venda)
- [ ] API: CRUD
- [ ] Logica de validade e status

### Garantias (3 paginas)
- [ ] Index: listagem com filtros
- [ ] Create: formulario vinculado a OS/cliente/veiculo
- [ ] Show: visualizacao com status
- [ ] API: CRUD
- [ ] Verificacao de expirado

### Compras (3 paginas)
- [ ] Index: listagem com filtros
- [ ] Create: formulario com itens e fornecedor
- [ ] Show: visualizacao detalhada
- [ ] API: CRUD
- [ ] Entrada automatica no estoque ao receber

---

## FASE 5: Financeiro

### Lancamentos Financeiros (3 paginas)
- [ ] Index: listagem com filtros (a pagar/a receber, status)
- [ ] Create: formulario de lancamento
- [ ] Show: visualizacao com opcao de baixa
- [ ] API: CRUD + endpoint de pagamento (pay)
- [ ] Integracao com vendas/compras/OS

### Caixa (3 paginas)
- [ ] Index: listagem de caixas (abertos/fechados)
- [ ] Open: abertura de caixa com saldo inicial
- [ ] Show: visualizacao com transacoes e fechamento
- [ ] API: CRUD + open/close
- [ ] Calculo automatico de saldo

### Relatorios (1 pagina)
- [ ] Dashboard de relatorios
- [ ] Relatorio de vendas por periodo
- [ ] Relatorio de compras por periodo
- [ ] Relatorio financeiro (receitas x despesas)
- [ ] Exportacao (PDF ou planilha — opcional)

---

## FASE 6: Administracao

### Usuarios (3 paginas)
- [ ] Index: listagem de usuarios da empresa
- [ ] Create: convite/cadastro de usuario
- [ ] Edit: edicao de permissoes/role
- [ ] API: CRUD via Supabase Auth Admin
- [ ] Controle de roles (admin, manager, user)

---

## FASE 7: Finalizacao

### Testes
- [ ] Testar todos os fluxos CRUD
- [ ] Testar multi-tenancy (isolamento entre empresas)
- [ ] Testar autenticacao (login/logout/sessao expirada)
- [ ] Testar responsividade mobile
- [ ] Testar permissoes por role

### Deploy Producao
- [ ] Configurar dominio customizado no Vercel (opcional)
- [ ] Configurar variaveis de producao no Vercel
- [ ] Migrar dados do SQLite para Supabase (se houver dados)
- [ ] DNS do dominio apontando para Vercel
- [ ] Testar em producao

### Limpeza
- [ ] Remover arquivos de debug (teste.php, teste.html, fix-permissions.php)
- [ ] Remover workflow de FTP deploy (.github/workflows/deploy.yml)
- [ ] Atualizar README com nova stack

---

## Resumo de Numeros

| Item | Quantidade |
|---|---|
| Tabelas Supabase | 23 |
| Paginas React | 43 |
| API Routes | ~15 |
| RLS Policies | ~20 |
| Fases | 7 |

## Ordem de Execucao Recomendada

1. Setup (Supabase + Next.js + Vercel + GitHub) — **1 dia**
2. Auth + Layout + Componentes base — **2-3 dias**
3. Cadastros (Clientes, Veiculos, Produtos, Fornecedores) — **3-4 dias**
4. Operacional (OS, Estoque, Vendas, Orcamentos, Garantias, Compras) — **5-7 dias**
5. Financeiro (Lancamentos, Caixa, Relatorios) — **3-4 dias**
6. Admin (Usuarios) — **1 dia**
7. Testes + Deploy — **1-2 dias**

**Total estimado: 16-22 dias de desenvolvimento**
