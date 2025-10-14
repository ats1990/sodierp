<div class="modal fade" id="viewAlunosModal" tabindex="-1" aria-labelledby="viewAlunosModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header bg-primary text-white">
<h5 class="modal-title" id="viewAlunosModalLabel">
<i class="mdi mdi-account-group-outline"></i> Alunos da Turma: <span id="modalTurmaNome" class="fw-bold"></span>
</h5>
<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
<div class="mb-3">
<p class="mb-1"><strong>Professor(a) Responsável:</strong> <span id="modalProfessorNome">...</span></p>
<p><strong>Total de Alunos:</strong> <span id="modalAlunosCount">0</span></p>
</div>

            <hr>

            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nome Completo</th>
                            <th>Idade</th>
                            <th>Celular</th>
                            <th>Mão Dominante</th>
                        </tr>
                    </thead>
                    {{-- O corpo da tabela será preenchido via JavaScript --}}
                    <tbody id="modalAlunosTableBody">
                        <tr>
                            <td colspan="5" class="text-center text-muted">Carregando alunos...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div id="modalLoading" class="text-center p-4 d-none">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-primary">Buscando dados da turma...</p>
            </div>

            <div id="modalError" class="alert alert-danger d-none mt-3">
                <p class="mb-0 fw-bold">Erro ao carregar:</p>
                <span id="modalErrorMessage"></span>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        </div>
    </div>
</div>

</div>