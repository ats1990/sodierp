<div class="modal fade" id="modalAtribuicaoLogica" tabindex="-1" aria-labelledby="modalAtribuicaoLogicaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAtribuicaoLogicaLabel">Atribuição Rápida Inteligente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{-- A rota 'formacao.turmas.atribuir' agora aponta para o método lógico do Controller --}}
            <form action="{{ route('formacao.turmas.atribuir') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted">A Atribuição Lógica Inteligente priorizará a distribuição de alunos **sem turma**:</p>
                    <ol class="small">
                        <li>Alunos para turmas de **contra-turno** (ex: Manhã vai para Tarde).</li>
                        <li>**Canhotos** para turmas específicas, se configurado.</li>
                        <li>Os alunos restantes serão distribuídos **homogeneamente** em ordem alfabética.</li>
                    </ol>

                    <hr>
                    <h6 class="text-primary">Opções para Canhotos (Opcional)</h6>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="atribuir_canhotos_separadamente" value="1" id="atribuirCanhotosCheck">
                        <label class="form-check-label fw-bold" for="atribuirCanhotosCheck">
                            Deseja reservar turmas para alunos Canhotos?
                        </label>
                    </div>

                    <div id="opcoesCanhotos" style="display: none; padding-left: 1rem; border-left: 3px solid #ffc107; background-color: #fffbe6; padding-top: 10px; padding-bottom: 5px; border-radius: 5px;">
                        <p class="text-warning small">As turmas selecionadas abaixo receberão **apenas** os alunos canhotos, respeitando o limite de vagas.</p>
                        
                        <div class="mb-3">
                            <label for="turma_canhoto_manha" class="form-label small fw-bold">Turma para Canhotos do turno da **Manhã**</label>
                            <select name="turma_canhoto_manha" id="turma_canhoto_manha" class="form-select form-select-sm">
                                <option value="">Não reservar turma específica</option>
                                @foreach($turmas as $turma)
                                    @if($turma->periodo === 'Manhã')
                                        <option value="{{ $turma->id }}">{{ $turma->nomeCompleto }} (Vagas: {{ $turma->vagas - $turma->alunos()->count() }})</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="turma_canhoto_tarde" class="form-label small fw-bold">Turma para Canhotos do turno da **Tarde**</label>
                            <select name="turma_canhoto_tarde" id="turma_canhoto_tarde" class="form-select form-select-sm">
                                <option value="">Não reservar turma específica</option>
                                @foreach($turmas as $turma)
                                    @if($turma->periodo === 'Tarde')
                                        <option value="{{ $turma->id }}">{{ $turma->nomeCompleto }} (Vagas: {{ $turma->vagas - $turma->alunos()->count() }})</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Iniciar Atribuição Rápida</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkbox = document.getElementById('atribuirCanhotosCheck');
        const opcoesDiv = document.getElementById('opcoesCanhotos');
        
        const toggleOpcoes = (checked) => {
            opcoesDiv.style.display = checked ? 'block' : 'none';
        };

        checkbox.addEventListener('change', function() {
            toggleOpcoes(this.checked);
        });
        
        // Garante o estado inicial (caso haja erro de validação e o modal reabra)
        toggleOpcoes(checkbox.checked);
    });
</script>
@endpush
