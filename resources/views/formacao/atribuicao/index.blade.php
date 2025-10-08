@extends('layouts.app') 

@section('content')
<div class="page-header">
    <h3 class="page-title">Atribuição Detalhada de Turmas</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('painel.coordenacao') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('formacao.turmas.index') }}">Turmas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Atribuição de Alunos</li>
        </ol>
    </nav>
</div>

<!-- =================================================== -->
<!-- ÁREA DE MENSAGENS (Sucesso, Erro ou Validação) -->
<!-- =================================================== -->
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {!! session('success') !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error') || $errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Ops! Algo deu errado:</strong>
        @if (session('error'))
            <p>{!! session('error') !!}</p>
        @endif
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Lista de Alunos ({{ $alunos->total() }} encontrados)</h4>
                <p class="card-description">Gerencie a atribuição de turmas por aluno individualmente.</p>

                <!-- =================================================== -->
                <!-- BLOCO DE FILTROS -->
                <!-- =================================================== -->
                <form method="GET" action="{{ route('formacao.atribuicao.index') }}" class="mb-4">
                    <div class="row align-items-end g-2">
                        
                        {{-- 1. Filtro por Turma --}}
                        <div class="col-12 col-md-3">
                            <label for="filtro_turma" class="form-label small fw-bold">Filtrar por Turma</label>
                            <select name="filtro_turma" id="filtro_turma" class="form-select form-select-sm">
                                <option value="" selected>Todas as Turmas/Situações</option>
                                <option value="sem_turma" {{ request('filtro_turma') == 'sem_turma' ? 'selected' : '' }}>-- Sem Turma (Disponíveis) --</option>
                                @foreach ($turmas as $turma)
                                    <option value="{{ $turma->id }}" {{ request('filtro_turma') == $turma->id ? 'selected' : '' }}>
                                        {{ $turma->nome_completo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- 2. Filtro por Ano Letivo --}}
                        <div class="col-12 col-md-2">
                            <label for="filtro_ano" class="form-label small fw-bold">Ano Letivo</label>
                            <select name="filtro_ano" id="filtro_ano" class="form-select form-select-sm">
                                <option value="" selected>Todos os Anos</option>
                                @if(isset($anosDisponiveis))
                                    @foreach($anosDisponiveis as $ano)
                                        <option value="{{ $ano }}" {{ request('filtro_ano') == $ano ? 'selected' : '' }}>{{ $ano }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        {{-- 3. Campo de Busca por Nome/CPF --}}
                        <div class="col-12 col-md-4">
                            <label for="search" class="form-label small fw-bold">Buscar Aluno (Nome ou CPF)</label>
                            <input type="text" name="search" id="search" class="form-control form-control-sm" placeholder="Digite nome ou CPF" value="{{ request('search') }}">
                        </div>

                        {{-- 4. Botões de Ação --}}
                        <div class="col-12 col-md-3 d-flex">
                            <button type="submit" class="btn btn-primary btn-sm me-2 w-50">
                                <i class="mdi mdi-filter-outline"></i> Filtrar
                            </button>
                            <a href="{{ route('formacao.atribuicao.index') }}" class="btn btn-secondary btn-sm w-50">
                                <i class="mdi mdi-close-circle-outline"></i> Limpar
                            </a>
                        </div>
                    </div>
                </form>
                
                <!-- =================================================== -->
                <!-- FIM DO BLOCO DE FILTROS -->
                <!-- =================================================== -->

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Aluno</th>
                                <th>CPF</th>
                                <th class="text-center">Turma Atual</th>
                                <th class="text-center">Atribuição</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($alunos as $aluno)
                                <tr>
                                    <td class="text-wrap">
                                        <div class="d-flex align-items-center">
                                            <i class="mdi mdi-account-circle mdi-24px me-2 text-primary"></i>
                                            <span class="fw-bold">{{ $aluno->nomeCompleto }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $aluno->cpf }}</td>
                                    <td class="text-center">
                                        @if($aluno->turma)
                                            <span class="badge bg-success">{{ $aluno->turma->nome_completo }}</span>
                                        @else
                                            <span class="badge bg-warning text-dark">DISPONÍVEL</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{-- Formulário de Atribuição --}}
                                        <form action="{{ route('formacao.turmas.atribuir') }}" method="POST" class="d-flex align-items-center">
                                            @csrf
                                            {{-- Campo oculto para o aluno_id --}}
                                            <input type="hidden" name="aluno_id" value="{{ $aluno->id }}">
                                            
                                            <select name="turma_id" class="form-select form-select-sm me-2">
                                                <option value="" {{ is_null($aluno->turma_id) ? 'selected' : '' }}>
                                                    -- Desvincular / Sem Turma --
                                                </option>
                                                
                                                @foreach ($turmas as $turma)
                                                    <option value="{{ $turma->id }}" {{ $aluno->turma_id == $turma->id ? 'selected' : '' }}>
                                                        {{ $turma->nome_completo }} ({{ $turma->alunos->count() }}/{{ $turma->vagas }} vagas)
                                                    </option>
                                                @endforeach
                                            </select>

                                            {{-- Botão de Atribuição --}}
                                            <button type="submit" class="btn btn-success btn-sm flex-shrink-0" title="Salvar Atribuição">
                                                <i class="mdi mdi-check"></i> Salvar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            
                            @if($alunos->isEmpty())
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        Nenhum aluno encontrado com os filtros aplicados.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                {{-- Paginação --}}
                <div class="mt-4">
                    {{ $alunos->appends(request()->query())->links() }} 
                    {{-- Usamos appends() para manter os filtros na paginação --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
