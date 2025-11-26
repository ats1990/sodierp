@extends('layouts.app')

@section('styles')
<style>
    /* -------------------------------------------------------------------
   ESTILOS ESPECÍFICOS PARA ORDENAÇÃO
   ------------------------------------------------------------------- */

    /* Estilo para o link de ordenação */
    .sortable-header {
        text-decoration: none;
        color: #212529;
        /* Cor do texto padrão */
        display: inline-flex;
        /* Permite alinhar texto e ícone */
        align-items: center;
        gap: 5px;
        /* Espaço entre o texto e o ícone */
        width: 100%;
        /* O link ocupa toda a célula TH */
    }

    .sortable-header:hover {
        color: #f47034;
        /* Cor de hover */
    }

    /* Estilo para a seta de ordenação ativa */
    .sortable-icon {
        color: #f47034;
        /* Cor da Marca/Flamingo para destaque */
        font-size: 1rem;
        /* Tamanho ligeiramente maior para MDI */
    }

    /* Garante que o ícone genérico (não ativo) também tenha a cor padrão */
    .sortable-header:not(.active) .sortable-icon {
        color: #6c757d;
        /* Cor mais suave para ícones inativos */
        opacity: 0.5;
        /* Deixa o ícone de sort genérico mais sutil */
    }

    /* Outros estilos de paginação e botões */
    .color-brand {
        color: #f47034 !important;
    }

    .btn-flamingo {
        background-color: #f47034 !important;
        border-color: #f47034 !important;
        color: white !important;
    }

    /* ... (Restante do CSS de Paginação que você usa) ... */
    .pagination {
        justify-content: center;
    }

    .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .pagination .page-link i,
    .pagination .page-link svg,
    .pagination .page-link span {
        font-size: 1rem !important;
    }

    .page-item.active .page-link {
        background-color: #f47034;
        border-color: #f47034;
    }

    .page-link:hover {
        color: #f47034;
    }

    /* NOVO: Ajuste para os dropdowns ficarem menores e alinhados */
    .form-select.form-select-sm.w-auto {
        max-width: 150px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <h1>Lista de Alunos</h1>

    {{-- BLOCO DE FILTROS/AÇÕES - ATUALIZADO COM FILTROS HISTÓRICOS --}}
    <div class="mb-3 d-flex justify-content-between align-items-center">

        {{-- Botões de AÇÕES GLOBAIS --}}
        <div class="d-flex gap-2">
            <a href="{{ route('aluno.import.form') }}" class="btn btn-flamingo">
                <i class="mdi mdi-upload me-2"></i> Importar Alunos
            </a>
            <a href="{{ route('aluno.create') }}" class="btn btn-flamingo">
                <i class="mdi mdi-plus me-2"></i> Novo Aluno
            </a>
        </div>

        {{-- NOVO FORMULÁRIO DE FILTRO COMPLETO (Ano, Período, Turma) --}}
        <form method="GET" action="{{ route('aluno.index') }}" class="d-flex align-items-center gap-3">

            {{-- 1. NOVO FILTRO: Ano Letivo (usa $anosLetivos do Controller) --}}
            <label for="ano_letivo" class="mb-0 fw-bold">Ano:</label>
            <select name="ano_letivo" id="ano_letivo" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                <option value="">Todos os Anos</option>
                {{-- Verifica se a variável existe antes de iterar (caso o Controller não a retorne) --}}
                @if(isset($anosLetivos))
                @foreach($anosLetivos as $ano)
                <option value="{{ $ano }}" {{ request('ano_letivo') == $ano ? 'selected' : '' }}>
                    {{ $ano }}
                </option>
                @endforeach
                @endif
            </select>

            {{-- 2. NOVO FILTRO: Período/Semestre (usa $periodos do Controller) --}}
            <label for="periodo" class="mb-0 fw-bold">Período:</label>
            <select name="periodo" id="periodo" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                <option value="">Todos os Períodos</option>
                @if(isset($periodos))
                @foreach($periodos as $p)
                {{-- Mapeia o valor do DB para um texto legível (igual ao Accessor no Turma.php) --}}
                @php
                $periodoTexto = match((string)$p) {
                '1' => '1º Semestre',
                '2' => '2º Semestre',
                'Manhã', 'M' => 'Manhã',
                'Tarde', 'T' => 'Tarde',
                default => $p,
                };
                @endphp
                <option value="{{ $p }}" {{ request('periodo') == $p ? 'selected' : '' }}>
                    {{ $periodoTexto }}
                </option>
                @endforeach
                @endif
            </select>

            {{-- 3. FILTRO ESPECÍFICO: Por Turma ID (usa $turmas do Controller) --}}
            <label for="turma_id" class="mb-0 fw-bold">Turma Específica:</label>
            <select name="turma_id" id="turma_id" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                <option value="">Todas as Turmas (Detalhado)</option>
                @foreach($turmas as $turma)
                <option value="{{ $turma->id }}" {{ request('turma_id') == $turma->id ? 'selected' : '' }}>
                    {{-- Usa o Accessor nomeCompleto que ajustamos: Ex: 2024 / 2º Semestre - Turma A --}}
                    {{ $turma->nomeCompleto ?? $turma->letra }}
                </option>
                @endforeach
            </select>

            {{-- Campos Ocultos para MANTER a ORDENAÇÃO ao aplicar o filtro --}}
            @if(request()->has('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
            <input type="hidden" name="direction" value="{{ request('direction') }}">
            @endif
        </form>
    </div>

    {{-- Exibe mensagens de sucesso e erros... --}}
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('import_errors'))
    <div class="alert alert-danger">
        <p><strong>Erros na Importação:</strong></p>
        <ul>
            @foreach(session('import_errors') as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Tabela de Alunos --}}
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                {{-- ID --}}
                <th>
                    @php
                    $isCurrentSort = request('sort') == 'id';
                    $direction = $isCurrentSort && request('direction') == 'asc' ? 'desc' : 'asc';
                    $iconClass = $isCurrentSort ? (request('direction') == 'asc' ? 'mdi-sort-ascending' : 'mdi-sort-descending') : 'mdi-sort';
                    $activeClass = $isCurrentSort ? 'active' : '';
                    @endphp
                    <a href="{{ route('aluno.index', array_merge(request()->query(), ['sort' => 'id', 'direction' => $direction])) }}" class="sortable-header {{ $activeClass }}">
                        ID
                        <i class="mdi {{ $iconClass }} sortable-icon"></i>
                    </a>
                </th>

                {{-- NOME COMPLETO --}}
                <th>
                    @php
                    $isCurrentSort = request('sort') == 'nomeCompleto';
                    $direction = $isCurrentSort && request('direction') == 'asc' ? 'desc' : 'asc';
                    $iconClass = $isCurrentSort ? (request('direction') == 'asc' ? 'mdi-sort-ascending' : 'mdi-sort-descending') : 'mdi-sort';
                    $activeClass = $isCurrentSort ? 'active' : '';
                    @endphp
                    <a href="{{ route('aluno.index', array_merge(request()->query(), ['sort' => 'nomeCompleto', 'direction' => $direction])) }}" class="sortable-header {{ $activeClass }}">
                        Nome Completo
                        <i class="mdi {{ $iconClass }} sortable-icon"></i>
                    </a>
                </th>

                <th>CPF</th>

                {{-- TURMA (ORDENAÇÃO POR TURMA, que no Controller ordena por ano/letra) --}}
                <th>
                    @php
                    $isCurrentSort = request('sort') == 'turma_id';
                    $direction = $isCurrentSort && request('direction') == 'asc' ? 'desc' : 'asc';
                    $iconClass = $isCurrentSort ? (request('direction') == 'asc' ? 'mdi-sort-ascending' : 'mdi-sort-descending') : 'mdi-sort';
                    $activeClass = $isCurrentSort ? 'active' : '';
                    @endphp
                    <a href="{{ route('aluno.index', array_merge(request()->query(), ['sort' => 'turma_id', 'direction' => $direction])) }}" class="sortable-header {{ $activeClass }}">
                        Turma
                        <i class="mdi {{ $iconClass }} sortable-icon"></i>
                    </a>
                </th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($alunos as $aluno)
            <tr>
                <td>{{ $aluno->id }}</td>
                <td>{{ $aluno->nomeCompleto }}</td>
                <td>{{ $aluno->cpf }}</td>
                <td>
                    {{-- Usa a relação 'turma' e o Accessor 'nomeCompleto' --}}
                    {{ $aluno->turma?->nomeCompleto ?? 'N/A' }}
                </td>

                {{-- BOTÕES DE AÇÃO POR ALUNO --}}
                <td>
                    <div class="btn-group" role="group" aria-label="Ações do Aluno">
                        <a href="{{ route('aluno.show', $aluno) }}" class="btn btn-sm btn-primary" title="Ver Perfil Detalhado">
                            <i class="mdi mdi-eye"></i> Ver Perfil
                        </a>
                        <a href="{{ route('aluno.edit', $aluno) }}" class="btn btn-sm btn-warning" title="Editar Dados do Aluno">
                            <i class="mdi mdi-pencil"></i> Editar
                        </a>
                    </div>
                </td>

            </tr>
            @empty
            <tr>
                <td colspan="5">Nenhum aluno encontrado.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Links de Paginação --}}
    <div class="d-flex justify-content-center mt-4">
        {{ $alunos->links('pagination.custom') }}
    </div>
</div>
@endsection