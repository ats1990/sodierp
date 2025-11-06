@extends('layouts.app') 

@section('content')
<div class="container-fluid">
    <h1>Lista de Alunos</h1>
    
    {{-- Botão para a importação --}}
    <a href="{{ route('aluno.import.form') }}" class="btn btn-info mb-3">
        <i class="mdi mdi-upload me-2"></i> Importar Alunos
    </a>

    {{-- Exibe a mensagem de sucesso da importação --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    {{-- Exibe a lista de erros da importação, se houver --}}
    @if(session('import_errors'))
        <div class="alert alert-warning">
            <p>Erros durante a importação (Apenas linhas sem erros foram salvas):</p>
            <div style="max-height: 200px; overflow-y: scroll; border: 1px solid #ccc; padding: 10px;">
                <ul>
                    @foreach (session('import_errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Tabela de Alunos --}}
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome Completo</th>
                <th>CPF</th>
                <th>Turma</th>
            </tr>
        </thead>
        <tbody>
            {{-- A variável $alunos foi passada pelo Controller --}}
            @forelse($alunos as $aluno)
            <tr>
                <td>{{ $aluno->id }}</td>
                {{-- Usa o Accessor nomeExibicao do seu Model Aluno --}}
                <td>{{ $aluno->nome_exibicao }}</td> 
                <td>{{ $aluno->cpf }}</td>
                {{-- Acessa o relacionamento turma() do Model Aluno --}}
                <td>{{ $aluno->turma->nome ?? 'N/A' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4">Nenhum aluno encontrado.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    {{-- Links de Paginação (Necessário porque o Controller usa paginate(20)) --}}
    <div class="d-flex justify-content-center">
        {{ $alunos->links() }}
    </div>
</div>
@endsection