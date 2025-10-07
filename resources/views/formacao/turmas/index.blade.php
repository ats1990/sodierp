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
<!-- =================================================== -->

<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <h4 class="card-title">Turmas Ativas ({{ $turmas->count() }})</h4>
                    
                    <div class="d-flex flex-wrap gap-2">
                        <!-- Botão que abre o modal de Criação de Turmas -->
                        <button type="button" class="btn btn-gradient-primary btn-fw" data-bs-toggle="modal" data-bs-target="#createTurmaModal">
                            <i class="mdi mdi-plus me-1"></i> Criar Novas Turmas
                        </button>
                        
                        <!-- NOVO BOTÃO DE EXCLUSÃO EM MASSA -->
                        @if(!$turmas->isEmpty())
                        <button type="button" class="btn btn-outline-danger btn-fw" data-bs-toggle="modal" data-bs-target="#deleteAllConfirmationModal">
                            <i class="mdi mdi-delete-sweep me-1"></i> Apagar Todas
                        </button>
                        @endif
                    </div>
                </div>

                <!-- Tabela de Turmas existentes -->
                @if($turmas->isEmpty())
                    <p class="text-info">Nenhuma turma criada ainda. Clique em "Criar Novas Turmas" para começar.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nome da Turma</th>
                                    <th>Professor</th> 
                                    <th>Ano Letivo</th> 
                                    <th>Período</th>
                                    <th>Início</th>
                                    <th>Fim</th>
                                    <th>Vagas</th>
                                    <th>Alunos (Inscritos)</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($turmas as $turma)
                                <tr>
                                    {{-- Exibe apenas o nome simplificado --}}
                                    <td class="text-danger">Turma {{ $turma->letra }}</td>
                                    
                                    {{-- Assume que a relação 'professor' está definida no modelo Turma --}}
                                    <td>
                                        {{ $turma->professor ? $turma->professor->nome : 'Aguardando atribuição' }}
                                    </td>
                                    
                                    <td>{{ $turma->ano_letivo }}</td>
                                    <td>{{ $turma->periodo }}</td>
                                    {{-- EXIBINDO AS NOVAS DATAS (usando Carbon para formatar) --}}
                                    <td>{{ \Carbon\Carbon::parse($turma->data_inicio)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($turma->data_fim)->format('d/m/Y') }}</td>
                                    
                                    <td>{{ $turma->vagas }}</td>
                                    
                                    <td>{{ $turma->alunos->count() }}</td> 
                                    
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-info me-1">Detalhes</button>
                                            
                                            <!-- FORMULÁRIO DE EXCLUSÃO INDIVIDUAL -->
                                            <form action="{{ route('formacao.turmas.destroy', $turma->id) }}" method="POST" class="delete-form">
                                                @csrf 
                                                @method('DELETE') 
                                                <button type="button" class="btn btn-sm btn-danger delete-button" 
                                                         data-turma-nome="Turma {{ $turma->letra }} ({{ $turma->ano_letivo }}/{{ $turma->periodo }})"
                                                         data-bs-toggle="modal" 
                                                         data-bs-target="#deleteConfirmationModal">
                                                    Excluir
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('modals')
    <!-- =================================================== -->
    <!-- MODAL DE CRIAÇÃO DE TURMAS (Com campos de Data) -->
    <!-- =================================================== -->
    <div class="modal fade" id="createTurmaModal" tabindex="-1" aria-labelledby="createTurmaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createTurmaModalLabel">Criação Rápida de Turmas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('formacao.turmas.store') }}" method="POST">
                    @csrf 
                    <div class="modal-body">
                        
                        <p class="text-danger">A média recomendada é de 32 alunos por turma. O sistema atribuirá as letras A, B, C... automaticamente.</p>
                        
                        <div class="row">
                            <!-- Ano Letivo -->
                            <div class="col-md-4 form-group mb-3">
                                <label for="ano_letivo">Ano Letivo</label>
                                <input type="number" name="ano_letivo" id="ano_letivo" class="form-control" value="{{ old('ano_letivo', date('Y')) }}" required>
                            </div>
                            <!-- Data de Início -->
                            <div class="col-md-4 form-group mb-3">
                                <label for="data_inicio">Data de Início</label>
                                <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="{{ old('data_inicio', now()->format('Y-m-d')) }}" required>
                            </div>
                            <!-- Data de Fim -->
                            <div class="col-md-4 form-group mb-3">
                                <label for="data_fim">Data de Fim</label>
                                <input type="date" name="data_fim" id="data_fim" class="form-control" value="{{ old('data_fim', now()->addMonths(6)->format('Y-m-d')) }}" required>
                            </div>
                            <!-- Vagas por Turma -->
                            <div class="col-12 form-group mb-3">
                                <label for="vagas_por_turma">Vagas por Turma</label>
                                <input type="number" name="vagas_por_turma" id="vagas_por_turma" class="form-control" value="{{ old('vagas_por_turma', 32) }}" required>
                            </div>
                        </div>
                        
                        <!-- Opções de Período e Quantidade -->
                        <div class="form-group mt-3">
                            <label>Configuração de Turmas e Períodos (Máx. 10 por período)</label>
                            
                            <!-- Manhã -->
                            <div class="input-group mb-3">
                                <span class="input-group-text bg-gradient-light text-dark">Manhã</span>
                                <input type="number" name="qtd_manha" class="form-control" placeholder="Número de Turmas (Ex: 5)" value="{{ old('qtd_manha', 5) }}" min="0" max="10">
                                <span class="input-group-text">Letras (A, B, C...)</span>
                            </div>

                            <!-- Tarde -->
                            <div class="input-group mb-3">
                                <span class="input-group-text bg-gradient-light text-dark">Tarde</span>
                                <input type="number" name="qtd_tarde" class="form-control" placeholder="Número de Turmas (Ex: 5)" value="{{ old('qtd_tarde', 5) }}" min="0" max="10">
                                <span class="input-group-text">Letras (Sequência)</span>
                            </div>

                            <!-- Noite (Opcional) -->
                            <div class="input-group">
                                <span class="input-group-text bg-gradient-light text-dark">Noite</span>
                                <input type="number" name="qtd_noite" class="form-control" placeholder="Número de Turmas (Ex: 0)" value="{{ old('qtd_noite', 0) }}" min="0" max="10">
                                <span class="input-group-text">Letras (Sequência)</span>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-gradient-primary">Gerar Turmas</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- =================================================== -->
    <!-- MODAL DE CONFIRMAÇÃO DE EXCLUSÃO INDIVIDUAL -->
    <!-- =================================================== -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirmação de Exclusão</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza de que deseja **EXCLUIR PERMANENTEMENTE** a turma:</p>
                    <h5 class="text-danger" id="turmaNomeToDelete"></h5>
                    <p class="mt-3">Esta ação não pode ser desfeita e pode afetar alunos ou dados relacionados. Confirme para prosseguir.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteButton">Excluir Turma</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- =================================================== -->
    <!-- NOVO MODAL DE CONFIRMAÇÃO DE EXCLUSÃO EM MASSA -->
    <!-- =================================================== -->
    <div class="modal fade" id="deleteAllConfirmationModal" tabindex="-1" aria-labelledby="deleteAllConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="deleteAllConfirmationModalLabel">🚨 AVISO: Exclusão em Massa!</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="deleteAllTurmasForm" action="{{ route('formacao.turmas.destroy.all') }}" method="POST">
                    @csrf 
                    <div class="modal-body">
                        <p>Você está prestes a **EXCLUIR TODAS AS {{ $turmas->count() }} TURMAS** do sistema.</p>
                        <h4 class="text-danger my-3">ESTA AÇÃO É IRREVERSÍVEL!</h4>
                        <p>Os **alunos existentes não serão deletados**, mas serão **desvinculados** de suas turmas. Confirme se é isso que você deseja fazer.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Sim, EXCLUIR TODAS AS TURMAS</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Variável para armazenar o formulário de exclusão individual
    let formToSubmit = null;
    
    // VERIFICAÇÃO DE DEPENDÊNCIA
    if (typeof bootstrap === 'undefined' || !bootstrap.Modal) {
        console.error("ERRO CRÍTICO: Objeto Bootstrap JS não encontrado. Verifique se o bundle JS do Bootstrap (v5+) está carregado corretamente.");
    } else {

        document.addEventListener('DOMContentLoaded', function() {
            
            // 1. Reabrir o modal de criação se houver erros de validação
            if (document.getElementById('validation-error-flag')) {
                var modal = new bootstrap.Modal(document.getElementById('createTurmaModal'));
                modal.show();
            }
            
            // 2. Lógica para o Modal de Confirmação de Exclusão INDIVIDUAL
            const deleteConfirmationModal = document.getElementById('deleteConfirmationModal');
            const confirmDeleteButton = document.getElementById('confirmDeleteButton');

            // Abre o modal de confirmação individual
            deleteConfirmationModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget; // Botão que acionou o modal
                const turmaNome = button.getAttribute('data-turma-nome');
                
                // Armazena a referência do formulário
                formToSubmit = button.closest('.delete-form');
                
                // Atualiza o nome da turma no modal
                const modalTurmaNome = deleteConfirmationModal.querySelector('#turmaNomeToDelete');
                modalTurmaNome.textContent = turmaNome;
            });

            // Evento de clique no botão de confirmação dentro do modal individual
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
            // quando o botão de submit dentro do modal é clicado. Nenhuma JS adicional complexa é necessária.
        });
    }
</script>
@endpush