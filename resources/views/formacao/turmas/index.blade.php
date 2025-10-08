@extends('layouts.app') 

@section('content')
<div class="page-header">
    <h3 class="page-title">Gerenciamento de Turmas de Formação</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('painel.coordenacao') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Turmas</li>
        </ol>
    </nav>
</div>

<!-- =================================================== -->
<!-- ÁREA DE MENSAGENS (Sucesso, Erro ou Validação) -->
<!-- =================================================== -->
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error') || $errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{-- Flag para o JS saber que houve erro de validação (para reabrir o modal de criação) --}}
        <div id="validation-error-flag" style="display:none;"></div> 
        <strong>Ops! Algo deu errado:</strong>
        @if (session('error'))
            <p>{{ session('error') }}</p>
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
                
                {{-- Novo bloco de Ações --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Turmas Registradas ({{ $turmas->count() }})</h4>
                    <div class="d-flex flex-wrap gap-2">
                        
                        {{-- NOVO BOTÃO: ACESSO À TELA DE ATRIBUIÇÃO DETALHADA --}}
                        <a href="{{ route('formacao.atribuicao.index') }}" class="btn btn-info btn-sm" title="Ir para a tela de atribuição detalhada por aluno com filtros">
                            <i class="mdi mdi-account-card-details-outline"></i> Atribuição Detalhada
                        </a>

                        {{-- BOTÃO: MODAL DE ATRIBUIÇÃO RÁPIDA (Aluno sem turma -> Turma) --}}
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#atribuirAlunoModal" title="Atribuir um aluno disponível a uma turma rapidamente">
                            <i class="mdi mdi-account-plus-outline"></i> Atribuir Rápido
                        </button>

                        {{-- BOTÃO: MODAL DE NOVA TURMA --}}
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createTurmaModal" title="Criar uma nova turma">
                            <i class="mdi mdi-plus-circle-outline"></i> Nova Turma
                        </button>

                        {{-- BOTÃO: MODAL DE EXCLUIR TODAS AS TURMAS --}}
                        @if ($turmas->isNotEmpty())
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteAllTurmasModal" title="Excluir todas as turmas (Ação perigosa)">
                                <i class="mdi mdi-delete-forever"></i> Excluir Tudo
                            </button>
                        @endif
                    </div>
                </div>
                
                <p class="card-description">Gerencie as turmas disponíveis para a formação. Use a coluna "Alunos" para ver a lista de alunos da turma.</p>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nome da Turma</th>
                                <th>Ano</th>
                                <th>Período</th>
                                <th class="text-center">Vagas</th>
                                <th class="text-center">Alunos</th>
                                <th>Professor(a)</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($turmas as $turma)
                                <tr>
                                    <td class="fw-bold">{{ $turma->nome_completo }}</td>
                                    <td>{{ $turma->ano_letivo }}</td>
                                    <td>{{ $turma->periodo }}</td>
                                    <td class="text-center">{{ $turma->vagas }}</td>
                                    <td class="text-center">
                                        {{-- Cálculo de Vagas com cores: Verde (disponível), Azul (cheio), Vermelho (excedido) --}}
                                        <span class="badge rounded-pill bg-{{ $turma->alunos->count() < $turma->vagas ? 'success' : ($turma->alunos->count() > $turma->vagas ? 'danger' : 'primary') }}">
                                            {{ $turma->alunos->count() }} / {{ $turma->vagas }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($turma->professor)
                                            {{ $turma->professor->nomeCompleto }}
                                        @else
                                            <span class="text-muted small">Não atribuído</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{-- Botão para a Exclusão Individual (Modal) --}}
                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteConfirmationModal"
                                            data-turma-nome="{{ $turma->nome_completo }}"
                                            data-turma-id="{{ $turma->id }}"
                                            title="Excluir Turma"
                                        >
                                            <i class="mdi mdi-delete-forever"></i>
                                        </button>
                                        
                                        {{-- Formulário de exclusão (necessário para o JS do modal) --}}
                                        <form method="POST" action="{{ route('formacao.turmas.destroy', $turma) }}" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            {{-- Este formulário é enviado pelo JS do modal --}}
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        Nenhuma turma registrada ainda. Clique em "Nova Turma" para começar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- =================================================== --}}
{{-- INCLUSÃO DOS MODAIS DE AÇÃO --}}
{{-- Os modais precisam ser incluídos fora do card-body/card para funcionar corretamente --}}
{{-- =================================================== --}}

{{-- Modal para Criar Nova Turma --}}
@include('formacao.turmas._createTurmaModal')

{{-- Modal para Atribuir Aluno Rápido --}}
@include('formacao.turmas._atribuirAlunoModal') 

{{-- Modal para Confirmação de Exclusão Individual --}}
@include('formacao.turmas._deleteConfirmationModal')

{{-- Modal para Confirmação de Exclusão em Massa --}}
@include('formacao.turmas._deleteAllConfirmationModal')

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteConfirmationModal = document.getElementById('deleteConfirmationModal');
        const confirmDeleteButton = document.getElementById('confirmDeleteButton');
        let formToSubmit = null; // Variável para armazenar o formulário a ser enviado

        if (deleteConfirmationModal && confirmDeleteButton) {
            // 1. Lógica para mostrar o modal de confirmação de exclusão individual
            deleteConfirmationModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget; // Botão que acionou o modal
                const turmaNome = button.getAttribute('data-turma-nome');
                
                // Armazena a referência do formulário
                // Procura o formulário de exclusão dentro da mesma linha (<tr>)
                formToSubmit = button.closest('tr').querySelector('.delete-form'); 
                
                // Atualiza o nome da turma no modal
                const modalTurmaNome = deleteConfirmationModal.querySelector('#turmaNomeToDelete');
                modalTurmaNome.textContent = turmaNome;
            });

            // 2. Evento de clique no botão de confirmação dentro do modal individual
            confirmDeleteButton.addEventListener('click', function() {
                if (formToSubmit) {
                    // Esconde o modal antes de enviar
                    var modalInstance = bootstrap.Modal.getInstance(deleteConfirmationModal);
                    modalInstance.hide();
                    
                    // Envia o formulário DELETE
                    formToSubmit.submit();
                }
            });
            
            // 3. Lógica para o Modal de Confirmação de Exclusão em MASSA
            // O formulário de exclusão em massa (#deleteAllTurmasForm) é enviado diretamente
            // quando o botão de submit dentro do modal é clicado.

        }
        
        // Se houver um erro de validação (retorno da função storeTurmas), garantir que o modal de criação seja exibido novamente.
        if (document.getElementById('validation-error-flag')) {
            var createModal = new bootstrap.Modal(document.getElementById('createTurmaModal'));
            createModal.show();
        }
    });
</script>
@endpush
@endsection
