@extends('layouts.app')

@section('title', 'Atribuição Detalhada de Turmas')

@section('content')
{{-- INÍCIO: BLOCO DA ÁRVORE DE NAVEGAÇÃO (BREADCRUMB) --}}
    @isset($breadcrumbs)
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            @foreach ($breadcrumbs as $item)
                @if ($item['route'])
                    <li class="breadcrumb-item"><a href="{{ route($item['route']) }}">{{ $item['name'] }}</a></li>
                @else
                    <li class="breadcrumb-item active" aria-current="page">{{ $item['name'] }}</li>
                @endif
            @endforeach
        </ol>
    </nav>
    @endisset
    {{-- FIM: BLOCO DA ÁRVORE DE NAVEGAÇÃO --}}

<div class="row">
<div class="col-lg-12 grid-margin stretch-card">
<div class="row">
<div class="col-lg-12 grid-margin stretch-card">
<div class="card">
<div class="card-body">
<h4 class="card-title">Atribuição Detalhada de Turmas</h4>
<p class="card-description">
Atribua a turma a cada aluno e use o botão "Salvar Alterações" no final da lista para confirmar.
</p>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <form action="{{ route('formacao.atribuicao.index') }}" method="GET" id="filterForm" class="d-flex align-items-center">
                    <div class="form-group mb-0 mr-3">
                        <label for="turma_id_filter" class="mb-0 mr-2">Filtrar Alunos por:</label>
                        <select name="turma_id" id="turma_id_filter" class="form-control form-control-sm">
                            <option value="todos" {{ $filtroTurmaId == 'todos' ? 'selected' : '' }}>Todos os Alunos</option>
                            <option value="nao_atribuidos" {{ $filtroTurmaId == 'nao_atribuidos' ? 'selected' : '' }}>Não Atribuídos ({{ $alunos->whereNull('turma_id')->count() }})</option>
                            @foreach ($turmas as $turma)
                                <option value="{{ $turma->id }}" {{ $filtroTurmaId == $turma->id ? 'selected' : '' }}>
                                    {{-- CORREÇÃO 1: nome -> nomeCompleto | OTIMIZAÇÃO 1: alunos->where()->count() -> alunos_count | CORREÇÃO 2: num_vagas -> vagas --}}
                                    {{ $turma->nomeCompleto }} ({{ $turma->alunos_count }} / {{ $turma->vagas }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary">Aplicar Filtro</button>
                </form>
            </div>

            <form action="{{ route('formacao.atribuicao.bulkUpdate') }}" method="POST">
                @csrf
                @method('PUT') {{-- Usa o método PUT, mas submetido via POST (padrão Laravel) --}}

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 40%;">Aluno</th>
                                <th style="width: 20%;">Turma Atual</th>
                                <th style="width: 40%;">Atribuir / Mudar Turma</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($alunos as $aluno)
                                <tr id="aluno-row-{{ $aluno->id }}">
                                    <td>{{ $aluno->nomeCompleto }}</td>
                                    <td>
                                        <span 
                                            class="badge {{ $aluno->turma_id ? 'badge-info' : 'badge-warning' }}">
                                            {{ $aluno->turma ? $aluno->turma->nomeCompleto : 'NÃO ATRIBUÍDO' }}
                                        </span>
                                    </td>
                                    <td>
                                        <select 
                                            class="form-control"
                                            name="alunos[{{ $aluno->id }}]" {{-- Envia como array: alunos[aluno_id] = turma_id --}}
                                        >
                                            <option value="0" {{ is_null($aluno->turma_id) ? 'selected' : '' }}>-- Não Atribuído --</option>
                                            @foreach ($turmas as $turma)
                                                <option 
                                                    value="{{ $turma->id }}" 
                                                    {{ $aluno->turma_id == $turma->id ? 'selected' : '' }}
                                                >
                                                    {{-- CORREÇÃO 3: nome -> nomeCompleto | OTIMIZAÇÃO 2: alunos->where()->count() -> alunos_count | CORREÇÃO 4: num_vagas -> vagas --}}
                                                    {{ $turma->nomeCompleto }} ({{ $turma->alunos_count }} / {{ $turma->vagas }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Nenhum aluno encontrado com este filtro.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 text-right">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="mdi mdi-content-save"></i> Salvar Alterações
                    </button>
                </div>
            </form>
            </div>
    </div>
</div>

</div>

@endsection

{{-- Removemos o @push('scripts') pois não usamos mais AJAX --}}