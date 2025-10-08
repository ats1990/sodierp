<div class="modal fade" id="atribuirAlunoModal" tabindex="-1" aria-labelledby="atribuirAlunoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="atribuirAlunoModalLabel">Atribuição Rápida de Aluno a Turma</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{-- A rota 'formacao.turmas.atribuir' é usada para a atribuição rápida --}}
            <form id="atribuirTurmaForm" action="{{ route('formacao.turmas.atribuir') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted">Selecione o aluno e a turma de destino. Apenas **alunos sem turma atribuída** estão disponíveis nesta lista rápida.</p>

                    <div class="mb-3">
                        <label for="aluno_id" class="form-label fw-bold">Aluno Disponível *</label>
                        {{-- O FormacaoController passa a variável $alunosDisponiveis para esta lista --}}
                        <select name="aluno_id" id="aluno_id" class="form-select" required>
                            <option value="" disabled selected>Selecione um aluno...</option>
                            {{-- Deve ser passado pela indexTurmas (FormacaoController) --}}
                            @if(isset($alunosDisponiveis))
                                @foreach($alunosDisponiveis as $aluno)
                                    <option value="{{ $aluno->id }}">{{ $aluno->nomeCompleto }} (CPF: {{ $aluno->cpf }})</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="turma_id_destino" class="form-label fw-bold">Turma de Destino *</label>
                         {{-- O FormacaoController passa a variável $turmas para esta lista --}}
                        <select name="turma_id" id="turma_id_destino" class="form-select" required>
                            <option value="" disabled selected>Selecione a turma...</option>
                            @if(isset($turmas))
                                @foreach($turmas as $turma)
                                    <option value="{{ $turma->id }}">{{ $turma->nome_completo }} ({{ $turma->alunos->count() }} / {{ $turma->vagas }} vagas)</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Atribuir Aluno</button>
                </div>
            </form>
        </div>
    </div>
</div>
