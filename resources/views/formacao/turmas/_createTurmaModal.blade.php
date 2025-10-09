<div class="modal fade" id="createTurmaModal" tabindex="-1" aria-labelledby="createTurmaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                {{-- Título atualizado para refletir a criação em lote --}}
                <h5 class="modal-title" id="createTurmaModalLabel">Criar Múltiplas Turmas em Lote (Novo Ano Letivo)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{-- ATENÇÃO: Rota alterada para 'storeBulk' --}}
            <form action="{{ route('formacao.turmas.storeBulk') }}" method="POST">
                @csrf
                <div class="modal-body">
                    {{-- Exibição de erros gerais, se houver --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <p>Opa! Encontramos os seguintes problemas na submissão:</p>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <p class="text-info small">Use este modo para criar rapidamente todas as turmas de um novo Ano Letivo com configurações uniformes. A **nomenclatura sequencial** (Turma A, Turma B, ...) será gerada automaticamente.</p>

                    <div class="row g-3">
                        {{-- 1. Ano Letivo --}}
                        <div class="col-md-6">
                            <label for="ano_letivo" class="form-label fw-bold">Ano Letivo *</label>
                            <input type="number" name="ano_letivo" id="ano_letivo" 
                                class="form-control @error('ano_letivo') is-invalid @enderror" 
                                placeholder="Ex: {{ date('Y') }}" 
                                value="{{ old('ano_letivo', date('Y')) }}" min="2000" max="2050" required>
                            @error('ano_letivo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        {{-- 2. Número de Vagas (vagas_geral) --}}
                        <div class="col-md-6">
                            <label for="vagas_geral" class="form-label fw-bold">Número de Vagas (Geral) *</label>
                            <input type="number" name="vagas_geral" id="vagas_geral" 
                                class="form-control @error('vagas_geral') is-invalid @enderror" 
                                placeholder="Capacidade máxima por turma" value="{{ old('vagas_geral') }}" min="1" required>
                            @error('vagas_geral') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <hr class="mt-4 mb-3">

                        {{-- 3. Qtd. de Turmas (Manhã) --}}
                        <div class="col-md-6">
                            <label for="quantidade_manha" class="form-label fw-bold">Qtd. de Turmas (Manhã) *</label>
                            <input type="number" name="quantidade_manha" id="quantidade_manha" 
                                class="form-control @error('quantidade_manha') is-invalid @enderror" 
                                value="{{ old('quantidade_manha', 0) }}" min="0" required>
                            @error('quantidade_manha') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        {{-- 4. Qtd. de Turmas (Tarde) --}}
                        <div class="col-md-6">
                            <label for="quantidade_tarde" class="form-label fw-bold">Qtd. de Turmas (Tarde) *</label>
                            <input type="number" name="quantidade_tarde" id="quantidade_tarde" 
                                class="form-control @error('quantidade_tarde') is-invalid @enderror" 
                                value="{{ old('quantidade_tarde', 0) }}" min="0" required>
                            @error('quantidade_tarde') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <hr class="mt-4 mb-3">

                        {{-- 5. Data Início --}}
                        <div class="col-md-6">
                            <label for="data_inicio" class="form-label fw-bold">Data de Início *</label>
                            <input type="date" name="data_inicio" id="data_inicio" 
                                class="form-control @error('data_inicio') is-invalid @enderror" 
                                value="{{ old('data_inicio') }}" required>
                            @error('data_inicio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- 6. Data Fim --}}
                        <div class="col-md-6">
                            <label for="data_fim" class="form-label fw-bold">Data de Conclusão *</label>
                            <input type="date" name="data_fim" id="data_fim" 
                                class="form-control @error('data_fim') is-invalid @enderror" 
                                value="{{ old('data_fim') }}" required>
                            {{-- Já havia um @error aqui, mas ajustei para usar invalid-feedback --}}
                            @error('data_fim') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Criar Turmas em Lote</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script para reabrir o modal automaticamente em caso de falha de validação --}}
@if ($errors->any() && Route::is('formacao.turmas.storeBulk'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var myModal = new bootstrap.Modal(document.getElementById('createTurmaModal'));
            myModal.show();
        });
    </script>
@endif
