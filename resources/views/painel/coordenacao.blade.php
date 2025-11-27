@extends('layouts.app')

@section('title', 'Painel da Coordenação')

@section('head')
    <style>
        /* Definindo as cores da paleta */
        :root {
            --color-pampa: #F4F0EB;       /* Fundo principal claro e elegante */
            --color-flamingo: #F05E7B;    /* Cor de destaque vibrante */
            --color-black: #1A1A1A;       /* Texto principal */
            --color-dark-grey: #4F4F4F;   /* Texto e bordas secundárias */
            --color-white: #FFFFFF;       /* Fundo dos cards */
        }

        /* Fundo Pampa para o corpo */
        body {
            background-color: var(--color-pampa);
            color: var(--color-black);
            font-family: 'Inter', sans-serif; /* Sugestão de fonte moderna e limpa */
        }

        /* 1. Cabeçalho e Navegação */
        .page-header {
            border-bottom: 1px solid #E0E0E0; /* Linha divisória sutil */
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
        }

        /* Estilo para o ícone do título (Destaque Flamingo) */
        .page-title .page-title-icon {
            background-color: var(--color-flamingo);
            color: var(--color-white);
            border-radius: 8px; /* Cantos arredondados */
            padding: 0.8rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .breadcrumb-item.active {
            color: var(--color-dark-grey);
        }

        /* 2. Estilo para os Cards (Fundo Branco com Sombra) */
        .card {
            border-radius: 12px;
            overflow: hidden;
            background-color: var(--color-white);
            color: var(--color-black);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); /* Sombra sutil e moderna */
            border: 1px solid #F0F0F0;
            transition: transform 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .card-body h4 {
            color: var(--color-dark-grey); /* Títulos de cards em cinza */
            font-weight: 500;
        }

        /* 3. Destaques (Métricas) */
        .metric-value {
            font-size: 2.8rem;
            font-weight: 700;
            color: var(--color-flamingo); /* VALORES PRINCIPAIS EM FLAMINGO */
            line-height: 1.2;
            margin-top: 0.5rem;
        }

        .metric-label {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--color-dark-grey);
            text-transform: uppercase;
        }

        /* Ajuste para o Card de Vendas/Matrículas (Como no seu print claro) */
        .card-big-metric {
            min-height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        /* 4. Estilos de Gráfico e Barras (Cor Flamingo) */
        .chart-bar-container .bar {
            background-color: var(--color-flamingo); /* Barras em Flamingo */
            border-radius: 3px;
        }

        .chart-bar-container .label {
            color: var(--color-dark-grey);
            font-size: 0.85rem;
        }

        /* Listas de Detalhes (Municípios, Escolaridade) */
        .detail-list li {
            padding: 0.5rem 0;
            border-bottom: 1px solid #F0F0F0;
            display: flex;
            justify-content: space-between;
        }

        .detail-list li:last-child {
            border-bottom: none;
        }
        
        /* Cor de destaque para porcentagens em listas */
        .detail-value {
            color: var(--color-flamingo);
            font-weight: 600;
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon me-2">
                <i class="mdi mdi-home"></i>
            </span> Dashboard Coordenação
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">
                    <span></span>Overview <i class="mdi mdi-alert-circle-outline icon-sm align-middle" style="color: var(--color-flamingo);"></i>
                </li>
            </ul>
        </nav>
    </div>

    <div class="row">
        {{-- Card de Matrículas Vigentes (Estilo Grande e Limpo) --}}
        <div class="col-md-4 stretch-card grid-margin">
            <div class="card card-big-metric">
                <div class="card-body">
                    <p class="metric-label">Matrículas Vigentes</p>
                    <h2 class="metric-value">339</h2>
                    <h6 class="card-text" style="color: var(--color-dark-grey);">Dados atualizados</h6>
                </div>
            </div>
        </div>

        {{-- Card de Vendas Semanais (Estilo Grande e Limpo) --}}
        <div class="col-md-4 stretch-card grid-margin">
            <div class="card card-big-metric">
                <div class="card-body">
                    <p class="metric-label">Vendas Semanais</p>
                    <h2 class="metric-value">R$ 15.000,00</h2>
                    <h6 class="card-text" style="color: var(--color-flamingo);">Aumento de 60%</h6>
                </div>
            </div>
        </div>
        
        {{-- Card de Novos Alunos (Estilo Grande e Limpo) --}}
        <div class="col-md-4 stretch-card grid-margin">
            <div class="card card-big-metric">
                <div class="card-body">
                    <p class="metric-label">Novos Alunos (Mês)</p>
                    <h2 class="metric-value">45</h2>
                    <h6 class="card-text" style="color: var(--color-dark-grey);">Média mensal: 40</h6>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Gráfico de Barras: Resumo por Mês/Turma (Referência da Imagem Original) --}}
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Resumo por Mês/Turma</h4>
                    <p class="card-description" style="color: var(--color-dark-grey);">Número de alunos por turma.</p>
                    
                    {{-- Simulação do Gráfico de Barras com cores Flamingo --}}
                    <div class="chart-bar-container d-flex justify-content-around align-items-end" style="height: 200px; padding-top: 1rem;">
                        @php
                            $turmaData = [
                                'TA' => 30, 'TB' => 29, 'TC' => 30, 'TD' => 29, 'TE' => 29,
                                'TF' => 26, 'TG' => 27, 'TH' => 25, 'TI' => 26, 'TJ' => 27, 
                                'TK' => 26, 'TL' => 27
                            ];
                            $maxValue = 30;
                        @endphp
                        @foreach($turmaData as $label => $value)
                            <div class="text-center" style="width: 6%; margin: 0 1%;">
                                <div class="bar" style="height: {{ ($value / $maxValue) * 100 }}%; margin-bottom: 5px; min-height: 5px;" title="{{ $value }}"></div>
                                <small class="label">{{ $label }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Detalhes: Sexo e Idade em Cards Separados --}}
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Sexo</h4>
                    <div class="d-flex justify-content-around mt-4">
                        <div class="text-center">
                            <p class="metric-label">Feminino</p>
                            <h3 class="metric-value" style="font-size: 1.8rem;">0,0%</h3>
                        </div>
                        <div class="text-center">
                            <p class="metric-label">Masculino</p>
                            <h3 class="metric-value" style="font-size: 1.8rem;">0,0%</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Idade</h4>
                    <div class="d-flex flex-wrap justify-content-between mt-3">
                        @foreach(['15 Anos', '16 Anos', '17 Anos', '18 Anos', '19 Anos', '20 Anos'] as $age)
                            <div class="text-center mb-3" style="flex-basis: 30%;">
                                <p class="mb-0" style="font-size: 0.85rem; color: var(--color-dark-grey);">{{ str_replace(' Anos', '', $age) }}</p>
                                <p class="detail-value" style="font-size: 1.1rem; margin-top: 3px;">0,0%</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Card de Municípios --}}
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Municípios</h4>
                    <p class="detail-value" style="margin-bottom: 1rem;">0,0%</p>
                    <ul class="list-unstyled detail-list">
                        <li><span>Diadema</span> <span class="detail-value">0,0%</span></li>
                        <li><span>São Paulo</span> <span class="detail-value">0,0%</span></li>
                        <li><span>S. Bernardo do Campo</span> <span class="detail-value">0,0%</span></li>
                        <li><span>Santo André</span> <span class="detail-value">0,0%</span></li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Card de Escolaridade --}}
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Escolaridade</h4>
                    <ul class="list-unstyled detail-list">
                        <li><span>1º Ano do Ensino Médio</span> <span class="detail-value">0,0%</span></li>
                        <li><span>2º Ano do Ensino Médio</span> <span class="detail-value">0,0%</span></li>
                        <li><span>3º Ano do Ensino Médio</span> <span class="detail-value">0,0%</span></li>
                        <li><span>Ensino Médio Completo</span> <span class="detail-value">0,0%</span></li>
                        <li><span>Cursando Ensino Superior</span> <span class="detail-value">0,0%</span></li>
                        <li><span>Ensino Superior Completo</span> <span class="detail-value">0,0%</span></li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Card de Cursos relacionados Sedegrom --}}
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Cursos relacionados à Sedegrom?</h4>
                    <div class="d-flex justify-content-around mt-5 mb-5">
                        <div class="text-center">
                            <p class="metric-label">SIM</p>
                            <h3 class="metric-value" style="font-size: 1.8rem;">0,0%</h3>
                        </div>
                        <div class="text-center">
                            <p class="metric-label">NÃO</p>
                            <h3 class="metric-value" style="font-size: 1.8rem;">0,0%</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Linha de Métricas Inferiores (Reprovações, Contratações, Desistências) --}}
    <div class="row mt-4 mb-4">
        <div class="col-md-4 text-center">
            <p class="metric-label">REPROVAÇÕES</p>
            <h2 class="metric-value" style="font-size: 2rem;">0</h2>
            <p style="color: var(--color-dark-grey);">0,00%</p>
        </div>
        <div class="col-md-4 text-center">
            <p class="metric-label">CONTRATAÇÕES</p>
            <h2 class="metric-value" style="font-size: 2rem;">0</h2>
            <p style="color: var(--color-dark-grey);">0,00%</p>
        </div>
        <div class="col-md-4 text-center">
            <p class="metric-label">DESISTÊNCIAS</p>
            <h2 class="metric-value" style="font-size: 2rem;">0</h2>
            <p style="color: var(--color-dark-grey);">0,00%</p>
        </div>
    </div>

@endsection