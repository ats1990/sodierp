<div class="modal fade" id="atribuirAlunoModal" tabindex="-1" aria-labelledby="atribuirAlunoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                {{-- Título atualizado para ser mais específico --}}
                <h5 class="modal-title" id="atribuirAlunoModalLabel">Atribuição Lógica Inteligente de Alunos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{-- A rota 'formacao.turmas.atribuir' agora aponta para o método lógico do Controller, executando a atribuição em lote --}}
            <form action="{{ route('formacao.turmas.atribuir') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted">A Atribuição Rápida Inteligente distribuirá **todos** os alunos **sem turma**, priorizando:</p>
                    <ol class="small">
                        <li>Alunos para turmas de **contra-turno** (ex: Manhã vai para Tarde).</li>
                        <li>**Canhotos** para turmas específicas, se configurado abaixo.</li>
                        <li>Os alunos restantes serão distribuídos **homogeneamente** em ordem alfabética.</li>
                    </ol>

                    <hr>
                    
                    {{-- Checkbox para Canhotos --}}
                    <div class="form-check p-3 mb-4 border border-secondary rounded-3">
                        {{-- 🚨 CORREÇÃO FINAL: Uso de JS INLINE (onchange) para garantir o toggle imediato, ignorando conflitos de carregamento de script. --}}
                        <input class="form-check-input" type="checkbox" name="atribuir_canhotos_separadamente" value="1" id="atribuirCanhotosCheck"
                               onchange="document.getElementById('opcoesCanhotos').style.display = this.checked ? 'block' : 'none';">
                        <label class="form-check-label fw-bold text-dark" for="atribuirCanhotosCheck">
                            Deseja reservar turmas específicas para alunos Canhotos?
                        </label>
                    </div>

                    {{-- Container para Opções de Canhotos (Será exibido/ocultado via JS) --}}
                    {{-- 🎨 CORREÇÃO DE ESTILO: Cores suaves para melhor visual. --}}
                    <div id="opcoesCanhotos" class="p-3 mb-3 border border-secondary rounded-3 bg-light-subtle" style="display: none;">
                        <p class="text-dark small fw-bold">Selecione as turmas que receberão **apenas** os alunos canhotos (exclusivo). Turmas reservadas no cadastro já estão pré-selecionadas.</p>
                        
                        {{-- Dropdown para Manhã --}}
                        <div class="mb-3">
                            <label for="turma_canhoto_manha" class="form-label small fw-bold">Turma para Canhotos do turno da **Manhã**</label>
                            <select name="turma_canhoto_manha" id="turma_canhoto_manha" class="form-select form-select-sm">
                                <option value="">Não reservar turma específica</option>
                                @if(isset($turmas))
                                    @foreach($turmas as $turma)
                                        @if($turma->periodo === 'Manhã')
                                            @php 
                                                $vagasRestantes = $turma->vagas - $turma->alunos->count();
                                                $vagasTexto = $vagasRestantes > 0 ? "Vagas: $vagasRestantes" : "LOTADA";
                                                $isSelected = isset($turma->is_canhoto_reserved) && $turma->is_canhoto_reserved ? 'selected' : '';
                                            @endphp
                                            <option value="{{ $turma->id }}" {{ $vagasRestantes <= 0 ? 'disabled' : '' }} {{ $isSelected }}>{{ $turma->nomeCompleto }} ({{ $vagasTexto }})</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        
                        {{-- Dropdown para Tarde --}}
                        <div class="mb-0">
                            <label for="turma_canhoto_tarde" class="form-label small fw-bold">Turma para Canhotos do turno da **Tarde**</label>
                            <select name="turma_canhoto_tarde" id="turma_canhoto_tarde" class="form-select form-select-sm">
                                <option value="">Não reservar turma específica</option>
                                @if(isset($turmas))
                                    @foreach($turmas as $turma)
                                        @if($turma->periodo === 'Tarde')
                                             @php 
                                                $vagasRestantes = $turma->vagas - $turma->alunos->count();
                                                $vagasTexto = $vagasRestantes > 0 ? "Vagas: $vagasRestantes" : "LOTADA";
                                                $isSelected = isset($turma->is_canhoto_reserved) && $turma->is_canhoto_reserved ? 'selected' : '';
                                            @endphp
                                            <option value="{{ $turma->id }}" {{ $vagasRestantes <= 0 ? 'disabled' : '' }} {{ $isSelected }}>{{ $turma->nomeCompleto }} ({{ $vagasTexto }})</option>
                                        @endif
                                    @endforeach
                                @endif
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
{{-- O bloco @push('js') foi removido e substituído por JS inline para garantir o funcionamento --}}
