\
@extends('layouts.erp')

@section('title', 'Dashboard')

@section('content')
<div class="page-heading">
    <div>
        <h1>Dashboard</h1>
        <p>
            {{ $currentCompany->trade_name ?? $currentCompany->name ?? 'Canal Som' }}
            <span>· acompanhamento da operação</span>
        </p>
    </div>

    <div class="page-actions">
        <a href="{{ route('vehicles.create') }}" class="btn btn-secondary">
            + Novo veículo
        </a>

        <button type="button" class="btn btn-primary" disabled title="Em desenvolvimento">
            Nova venda
        </button>
    </div>
</div>

<section class="metric-grid">
    <article class="metric-card">
        <span class="metric-label">Vendas pagas hoje</span>
        <strong class="metric-value success">R$ 0,00</strong>
        <small>Movimento do dia</small>
    </article>

    <article class="metric-card">
        <span class="metric-label">Vendas no mês</span>
        <strong class="metric-value">R$ 0,00</strong>
        <small>Total bruto registrado</small>
    </article>

    <article class="metric-card">
        <span class="metric-label">OS em aberto</span>
        <strong class="metric-value warning">0</strong>
        <small>Abertas, aguardando ou em execução</small>
    </article>

    <article class="metric-card">
        <span class="metric-label">Caixa</span>
        <strong class="metric-value muted-value">Não iniciado</strong>
        <small>Módulo de caixa será ativado depois</small>
    </article>

    <article class="metric-card">
        <span class="metric-label">A receber</span>
        <strong class="metric-value warning">R$ 0,00</strong>
        <small>Lançamentos pendentes</small>
    </article>

    <article class="metric-card">
        <span class="metric-label">A pagar</span>
        <strong class="metric-value danger">R$ 0,00</strong>
        <small>Lançamentos pendentes</small>
    </article>

    <article class="metric-card">
        <span class="metric-label">Estoque baixo</span>
        <strong class="metric-value danger">0</strong>
        <small>Produtos no mínimo ou abaixo</small>
    </article>

    <article class="metric-card quick-card">
        <span class="metric-label">Acesso rápido</span>
        <div class="quick-actions">
            <a href="{{ route('vehicles.create') }}" class="quick-btn">
                Novo veículo
            </a>

            <button type="button" class="quick-btn muted" disabled>
                Nova OS
            </button>
        </div>
    </article>
</section>

<section class="dashboard-section">
    <div class="section-header">
        <div>
            <h2>Serviços / instalações de hoje</h2>
            <p>Agenda operacional da filial.</p>
        </div>

        <button type="button" class="btn btn-light" disabled>
            Ver ordens
        </button>
    </div>

    <div class="table-card">
        <div class="table-scroll">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th>Horário</th>
                        <th>OS</th>
                        <th>Cliente</th>
                        <th>Veículo</th>
                        <th>Técnico</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="empty-row">
                        <td colspan="6">
                            Nenhum serviço agendado para hoje.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<section class="dashboard-section">
    <div class="section-header">
        <div>
            <h2>Últimas movimentações</h2>
            <p>Começaremos pelos veículos cadastrados na Canal Som.</p>
        </div>

        <a href="{{ route('vehicles.index') }}" class="btn btn-light">
            Ver veículos
        </a>
    </div>

    <div class="table-card">
        <div class="table-scroll">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th>Módulo</th>
                        <th>Situação</th>
                        <th>Próximo teste</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Veículos</strong></td>
                        <td><span class="status-pill success-pill">Ativo</span></td>
                        <td>Cadastro, marca/modelo FIPE e listagem</td>
                    </tr>
                    <tr>
                        <td><strong>Clientes</strong></td>
                        <td><span class="status-pill waiting-pill">Próximo</span></td>
                        <td>Cadastro completo e vínculo com veículos</td>
                    </tr>
                    <tr>
                        <td><strong>OS / PDV / Estoque</strong></td>
                        <td><span class="status-pill neutral-pill">Planejado</span></td>
                        <td>Ativar após homologarmos os cadastros</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
