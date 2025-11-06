{{-- Arquivo: resources/views/alunos/show.blade.php --}}

@extends('layouts.app') 

@section('content')
@php
    // Helper para converter a data de nascimento para o formato brasileiro
    $dataNascFormatada = $aluno->dataNascimento ? Carbon\Carbon::parse($aluno->dataNascimento)->format('d/m/Y') : 'N/D';
@endphp

<div class="container-fluid">
    {{-- Título e Botões de Ação (topo) --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Perfil do Aluno: {{ $aluno->nomeCompleto }}</h1>
        <div class="btn-group">
            <a href="{{ route('aluno.edit', $aluno) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Editar Dados</a>
            <button class="btn btn-success"><i class="fas fa-external-link-alt"></i> Encaminhar</button>
        </div>
    </div>

    <div class="row">
        {{-- COLUNA LATERAL ESQUERDA (Informações Básicas e Status) --}}
        <div class="col-md-3">
            <div class="card mb-3 shadow">
                <div class="card-body text-center">
                    {{--  --}}
                    <h5 class="mt-2">{{ $aluno->nomeCompleto }}</h5>
                    <p class="text-muted">Turma: **{{ $aluno->turma->getNomeCompletoAttribute() ?? 'N/D' }}**</p>
                    <p class="text-muted">Professor: {{ $aluno->turma->professor->name ?? 'N/D' }}</p>
                    <hr>

                    <h6 class="card-title text-start">Dados Pessoais</h6>
                    <ul class="list-unstyled text-start small">
                        <li><strong>Data Nasc:</strong> {{ $dataNascFormatada }}</li>
                        <li><strong>Idade:</strong> {{ $aluno->idade }} anos</li>
                        <li><strong>CPF:</strong> {{ $aluno->cpf ?? 'N/D' }}</li>
                        <li><strong>RG:</strong> {{ $aluno->rg ?? 'N/D' }}</li>
                        <li><strong>E-mail:</strong> {{ $aluno->email ?? 'N/D' }}</li>
                        <li><strong>Celular:</strong> {{ $aluno->celular ?? 'N/D' }}</li>
                    </ul>

                    <h6 class="card-title text-start mt-3">Status de Encaminhamento</h6>
                    <ul class="list-unstyled text-start small">
                        <li><strong>Em Processo:</strong> {{ $aluno->jaTrabalhou ? 'Sim' : 'Não' }}</li>
                        <li><strong>CTPS Assinada:</strong> {{ $aluno->ctpsAssinada ? 'Sim' : 'Não' }}</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- COLUNA PRINCIPAL (Pedagógico e Social) --}}
        <div class="col-md-9">
            
            {{-- INFORMAÇÕES PEDAGÓGICAS (Laranja conforme imagem) --}}
            <div class="card mb-3 bg-warning text-white shadow">
                <div class="card-body">
                    <h4 class="text-center">INFORMAÇÕES PEDAGÓGICAS</h4>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    {{-- MÉDIAS GERAIS (Exemplo da imagem) --}}
                    <div class="card text-center h-100 shadow">
                        <div class="card-body">
                            <p class="mb-0 text-muted">MÉDIA FINAL</p>
                            <h2 class="text-primary display-4">8,0</h2>
                            <p>DISCIPLINA</p>
                            <h2 class="text-secondary display-4">7,3</h2>
                            <p>COMPORTAMENTO</p>
                            <hr>
                            <p>Faltas Registradas: **{{ $aluno->presencas->where('status', 'falta')->count() }}**</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    {{-- GRÁFICO (Placeholder) --}}
                    <div class="card shadow">
                        <div class="card-body">
                            <p class="text-center text-muted">GRÁFICO DE DESEMPENHO - (Integração com Chart.js/etc.)</p>
                                                    </div>
                    </div>
                </div>
            </div>

            {{-- OBSERVAÇÕES E FAMILIARES --}}
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="card h-100 shadow">
                        <div class="card-body">
                            <strong>OBSERVAÇÕES DA EQUIPE PEDAGÓGICA:</strong>
                            <p>{{ $aluno->observacoes ?? 'Nenhuma observação registrada.' }}</p>
                            
                            <h5 class="mt-4">Familiares (Renda Total: R$ {{ number_format($aluno->familiares->sum('salarioBase'), 2, ',', '.') }})</h5>
                            <ul class="list-unstyled small">
                                @forelse ($aluno->familiares as $familiar)
                                    <li>- {{ $familiar->nomeCompleto }} ({{ $familiar->parentesco }}). Salário: R$ {{ number_format($familiar->salarioBase, 2, ',', '.') }}</li>
                                @empty
                                    <li>Nenhum familiar cadastrado.</li>
                                @endforelse
                            </ul>
                            <a href="{{ route('aluno.edit', $aluno) }}#familiares" class="btn btn-sm btn-info mt-2">Gerenciar Familiares</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 shadow">
                        <div class="card-body">
                            <strong>SUGESTÕES DE ÁREAS DE ATUAÇÃO:</strong>
                            <p>#N/D (Baseado em notas e observações)</p>
                            
                            <h5 class="mt-4">Ocorrências</h5>
                            <div class="text-center display-4 mb-0">0</div> {{-- Placeholder --}}
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

@endsection