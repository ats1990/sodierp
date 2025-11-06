@extends('layouts.app') 

@section('content')
<div class="container-fluid">
    <h1>Lista de Alunos</h1>
    
    {{-- Bloco de botões de AÇÕES GLOBAIS (fora da tabela) --}}
    {{-- Usando a classe 'mb-3' e 'gap-2' do Bootstrap para espaçamento --}}
    <div class="mb-3 d-flex gap-2">
        
        {{-- Botão "Importar Alunos" (Estilo original: btn-info) --}}
        <a href="{{ route('aluno.import.form') }}" class="btn btn-info">
            <i class="mdi mdi-upload me-2"></i> Importar Alunos
        </a>

        {{-- Botão "Novo Aluno" (Estilizado para ser parecido: usando btn-primary ou btn-info) --}}
        {{-- Mantenho a cor 'btn-success' para diferenciar de Importar, mas com a mesma estrutura --}}
        <a href="{{ route('aluno.create') }}" class="btn btn-success">
            <i class="mdi mdi-plus me-2"></i> Novo Aluno
        </a>
        
    </div>
    
    {{-- Exibe a mensagem de sucesso da importação e erros... (código omitido para brevidade) --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
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
                <th>Ações</th> {{-- Coluna para os botões por aluno --}}
            </tr>
        </thead>
        <tbody>
            @forelse($alunos as $aluno)
            <tr>
                <td>{{ $aluno->id }}</td>
                <td>{{ $aluno->nomeCompleto }}</td>
                <td>{{ $aluno->cpf }}</td>
                <td>{{ $aluno->turma->nome ?? 'N/A' }}</td>
                
                {{-- BOTÕES DE AÇÃO POR ALUNO --}}
                <td>
                    <div class="btn-group" role="group" aria-label="Ações do Aluno">
                        {{-- Botão de Visualizar (Ver Perfil) --}}
                        <a href="{{ route('aluno.show', $aluno) }}" class="btn btn-sm btn-primary" title="Ver Perfil Detalhado">
                            <i class="fas fa-eye"></i> Ver Perfil
                        </a>
                        
                        {{-- Botão de Editar --}}
                        <a href="{{ route('aluno.edit', $aluno) }}" class="btn btn-sm btn-warning" title="Editar Dados do Aluno">
                            <i class="fas fa-edit"></i> Editar
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
    <div class="d-flex justify-content-center">
        {{ $alunos->links() }}
    </div>
</div>
@endsection