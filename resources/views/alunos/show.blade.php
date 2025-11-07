{{-- Arquivo: resources/views/alunos/show.blade.php --}}

@extends('layouts.app')

{{-- ADICIONANDO CSS PERSONALIZADO (LARANJA MARCA E CORREÇÃO DE PAGINAÇÃO) DIRETAMENTE NA PÁGINA --}}
@section('styles')
<style>
    /* Cor Principal da Marca/Flamingo */
    .color-brand {
        color: #f47034 !important; /* Cor dos números (8,0, 7,6, Faltas) */
    }

    /* Cor Laranja Flamingo para background */
    .bg-flamingo {
        background-color: #f47034 !important; /* Código HEX da Cor de Marca */
    }

    /* Definindo o estilo do botão Flamingo e garantindo que o texto dele seja branco */
    .btn-flamingo {
        background-color: #f47034 !important;
        border-color: #f47034 !important;
        color: white !important;
    }
    
    /* Garantindo que os Cards fiquem brancos com sombra */
    .card {
        background-color: white;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }

    /* * CORREÇÃO DA PAGINAÇÃO QUEBRADA E TAMANHO DA SETA 
     * (Incluído aqui, mas deve ser movido para o arquivo de listagem de alunos - index.blade.php)
     */
    .pagination {
        display: flex; 
        padding-left: 0;
        list-style: none;
        border-radius: 0.25rem;
        justify-content: center; 
    }
    
    .page-item {
        margin: 0 2px; 
    }
    
    .page-link {
        display: block;
        padding: 0.5rem 0.75rem;
        line-height: 1.25;
        color: #6c757d; 
        background-color: #fff;
        border: 1px solid #dee2e6;
        text-decoration: none;
        /* Força um tamanho de fonte padrão menor no link, o que deve reduzir o tamanho das setas */
        font-size: 1rem; 
    }
    
    .page-item.active .page-link {
        z-index: 1;
        color: #fff;
        background-color: #f47034; 
        border-color: #f47034;
    }
    
    .page-link:hover {
        color: #f47034; 
    }
</style>
@endsection
{{-- FIM DO CSS INTEGRADO --}}


@section('content')
@php
use Carbon\Carbon;

// 1. Helper para converter a data de nascimento para o formato brasileiro
$dataNascFormatada = $aluno->dataNascimento
    ? Carbon::parse($aluno->dataNascimento)->format('d/m/Y')
    : 'N/D';

// 2. Cálculo da idade completa em anos e meses 
$dataNascimento = $aluno->dataNascimento ? Carbon::parse($aluno->dataNascimento) : null;
$idadeCompleta = $dataNascimento ? $dataNascimento->diff(Carbon::now()) : null;
$idadeAnosMeses = $idadeCompleta ? "{$idadeCompleta->y} anos e {$idadeCompleta->m} meses" : 'N/D';

// 3. Variáveis de Acesso Seguro e/ou Placeholder 
$turmaNome = $aluno->turma?->getNomeCompletoAttribute() ?? 'N/D';
$professorNome = $aluno->turma?->professor?->name ?? 'N/D';

// Os dados de responsável e contrato 
$celularResponsavel = $aluno->responsavel?->celular ?? '(11) 9 XXXX-XXXX';
$nomeResponsavel = $aluno->responsavel?->nomeCompleto ?? 'N/D';
$telefoneRecado = $aluno->responsavel?->telefoneRecado ?? 'N/D';
$empresaContrato = $aluno->empresaContrato?->nomeFantasia ?? 'Nenhuma'; 

// 4. LÓGICA DO NOME: Exibe Nome Civil, ou Nome Civil (Nome Social)
$nomeExibicao = $aluno->nomeCompleto; 
if (!empty($aluno->nome_social)) {
    $nomeExibicao = "{$aluno->nomeCompleto} ({$aluno->nome_social})";
}

@endphp

<div class="container-fluid">
    
    {{-- Título da Tela e Botões de Ação (topo) --}}
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h1>Perfil do Aluno: {{ $nomeExibicao }}</h1>
        {{-- BOTÃO EDITAR com a cor Flamingo e texto branco --}}
        <div class="btn-group">
            <a href="{{ route('aluno.edit', $aluno) }}" class="btn btn-flamingo"><i class="fas fa-edit"></i> Editar Dados</a>
            <button class="btn btn-success"><i class="fas fa-external-link-alt"></i> Encaminhar</button>
        </div>
    </div>
    
    {{-- LINHA DE CONTATOS E RESPONSÁVEIS (Blocos Laranja Flamingo) --}}
    <div class="row mb-4">
        {{-- Título da Formação (Bloco Escuro) --}}
        <div class="col-12 bg-dark text-white p-2">
            <h5 class="m-0">Formação Básica - 2º Sem/2024</h5>
        </div>

        {{-- Cabeçalho de Contato Flamingo (ADICIONADO text-white) --}}
        <div class="col-md-2 p-0">
            <div class="bg-flamingo text-white p-2 h-100 d-flex flex-column justify-content-center text-center">
                <small class="text-uppercase font-weight-bold">Celular</small>
                <strong class="h6 m-0">{{ $aluno->celular ?? 'N/D' }}</strong>
            </div>
        </div>
        <div class="col-md-3 p-0">
            <div class="bg-flamingo text-white p-2 h-100 d-flex flex-column justify-content-center text-center">
                <small class="text-uppercase font-weight-bold">E-mail</small>
                <strong class="h6 m-0">{{ $aluno->email ?? 'N/D' }}</strong>
            </div>
        </div>
        <div class="col-md-3 p-0">
            <div class="bg-flamingo text-white p-2 h-100 d-flex flex-column justify-content-center text-center">
                <small class="text-uppercase font-weight-bold">Celular do Responsável</small>
                <strong class="h6 m-0">{{ $celularResponsavel }}</strong>
            </div>
        </div>
        <div class="col-md-2 p-0">
            <div class="bg-flamingo text-white p-2 h-100 d-flex flex-column justify-content-center text-center">
                <small class="text-uppercase font-weight-bold">Nome do Responsável</small>
                <strong class="h6 m-0">{{ $nomeResponsavel }}</strong>
            </div>
        </div>
        <div class="col-md-2 p-0">
            <div class="bg-flamingo text-white p-2 h-100 d-flex flex-column justify-content-center text-center">
                <small class="text-uppercase font-weight-bold">Tel. Recado</small>
                <strong class="h6 m-0">{{ $telefoneRecado }}</strong>
            </div>
        </div>
    </div>
    {{-- FIM LINHA DE CONTATOS --}}
    
    <div class="row">
        {{-- COLUNA LATERAL ESQUERDA (Informações Básicas e Status) --}}
        <div class="col-md-3">
            {{-- Card Principal Lateral --}}
            <div class="card mb-3 shadow">
                <div class="card-body p-0">
                    {{-- Bloco Superior (Foto e Nome) --}}
                    <div class="p-3 text-center">
                        {{-- Placeholder para Foto do Aluno --}}
                        <div class="d-flex justify-content-center mb-2">
                            <div style="
                                width: 100px; 
                                height: 100px; 
                                border-radius: 50%; 
                                background-color: #e0e0e0; 
                                display: flex; 
                                align-items: center; 
                                justify-content: center; 
                                color: #999; 
                                font-size: 0.8rem; 
                                border: 2px solid #ccc;
                            ">
                                Foto N/D
                            </div>
                        </div>
                        
                        <h5 class="mt-2 mb-0">{{ $nomeExibicao }}</h5>
                        <p class="text-muted small mb-1">Turma: **{{ $turmaNome }}**</p>
                        <p class="text-muted small">Professor: {{ $professorNome }}</p>
                    </div>
                    
                    {{-- Bloco Encaminhar (Flamingo) --}}
                    <div class="bg-flamingo text-white text-center py-2 mb-3">
                        <strong class="h6 m-0 text-uppercase">Encaminhar</strong>
                    </div>

                    {{-- INFORMAÇÕES PESSOAIS E CAMPOS ADICIONADOS --}}
                    <div class="px-3">
                        <h6 class="card-title text-start font-weight-bold">Dados Pessoais</h6>
                        <ul class="list-unstyled text-start small">
                            <li><strong>Data Nasc:</strong> {{ $dataNascFormatada }}</li>
                            <li><strong>Idade:</strong> {{ $idadeAnosMeses }}</li> 
                            <li><strong>Dominância:</strong> {{ $aluno->mao_dominante ?? 'N/D' }}</li> 
                            <li><strong>CPF:</strong> {{ $aluno->cpf ?? 'N/D' }}</li>
                            <li><strong>RG:</strong> {{ $aluno->rg ?? 'N/D' }}</li>
                            <li><strong>Bairro:</strong> {{ $aluno->bairro ?? 'N/D' }}</li> 
                        </ul>
                        
                        <hr>
                    </div>

                    {{-- Bloco Contrato (Flamingo) --}}
                    <div class="bg-flamingo text-white text-center py-2 mb-3">
                        <strong class="h6 m-0 text-uppercase">Contrato com</strong>
                    </div>

                    {{-- Status Contrato --}}
                    <div class="px-3">
                        <p class="mb-3 small font-weight-bold text-center">{{ $empresaContrato }}</p>
                    </div>

                    {{-- Bloco Empresas Encaminhadas (Escuro) --}}
                    <div class="bg-dark text-white text-center py-2 mb-3">
                        <strong class="h6 m-0 text-uppercase">Empresas que já foi encaminhado</strong>
                    </div>

                    {{-- Empresas (CORRIGIDO) --}}
                    <div class="px-3 pb-3">
                        <p class="mb-0 small text-center">
                            @if ($aluno->encaminhamentos && $aluno->encaminhamentos->isNotEmpty())
                                {{ $aluno->encaminhamentos->pluck('empresa.nomeFantasia')->join(', ') }}
                            @else
                                Nenhuma
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- COLUNA PRINCIPAL (Pedagógico e Social) --}}
        <div class="col-md-9">

            {{-- TÍTULO: INFORMAÇÕES PEDAGÓGICAS (Flamingo) --}}
            <div class="card mb-3 bg-flamingo text-white shadow">
                <div class="card-body py-2">
                    <h5 class="text-center m-0 text-uppercase">Informações Pedagógicas</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    {{-- MÉDIAS GERAIS (Números com a cor da marca) --}}
                    <div class="card text-center h-100 shadow p-3">
                        <p class="mb-0 text-muted small">Disciplina e Média</p>
                        <h6 class="mb-0 font-weight-bold">MÉDIA FINAL</h6>
                        {{-- APLICANDO NOVO CSS color-brand --}}
                        <h1 class="color-brand display-4 mb-0">8,0</h1> 
                        <hr>
                        <h6 class="mb-0 font-weight-bold">COMPORTAMENTO</h1>
                        {{-- APLICANDO NOVO CSS color-brand --}}
                        <h1 class="color-brand display-4 mb-0">7,6</h1>
                        <p class="mt-2">Faltas Registradas: 
                            {{-- APLICANDO NOVO CSS color-brand --}}
                            <strong class="color-brand">
                                {{ optional($aluno->presencas)->where('status', 'falta')->count() ?? 0 }}
                            </strong>
                        </p>
                    </div>
                </div>

                <div class="col-md-9">
                    {{-- GRÁFICO (Placeholder) --}}
                    <div class="card shadow h-100">
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <p class="text-center text-muted">GRÁFICO DE DESEMPENHO AQUI</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- OBSERVAÇÕES, SUGESTÕES E CURSOS --}}
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card shadow p-3 mb-3">
                        <strong class="text-uppercase">OBSERVAÇÕES DA EQUIPE PEDAGÓGICA:</strong>
                        <p class="mt-2">{{ $aluno->observacoes ?? 'Nenhuma observação registrada.' }}</p>
                    </div>
                </div>
                
                <div class="col-md-12">
                    <div class="card shadow p-3 mb-3">
                        <strong class="text-uppercase">SUGESTÕES DE ÁREAS DE ATUAÇÃO:</strong>
                        <p class="mt-2">#N/D (Baseado em notas e observações)</p>
                    </div>
                </div>
                
                <div class="col-md-12">
                    <div class="card shadow p-3 mb-3">
                        <strong class="text-uppercase">CURSOS LIVRES:</strong>
                        {{-- CORRIGIDO --}}
                        <p class="mt-2">
                            @if ($aluno->cursosLivres && $aluno->cursosLivres->isNotEmpty())
                                {{ $aluno->cursosLivres->pluck('nome')->join(', ') }}
                            @else
                                Nenhum
                            @endif
                        </p>
                    </div>
                </div>
                {{-- O BLOCO FAMILIARES FOI REMOVIDO --}}
            </div>

        </div>
        {{-- FIM COLUNA PRINCIPAL --}}
    </div>
</div>

@endsection