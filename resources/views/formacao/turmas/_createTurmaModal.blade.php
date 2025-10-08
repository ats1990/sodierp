<div class="modal fade" id="createTurmaModal" tabindex="-1" aria-labelledby="createTurmaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createTurmaModalLabel">Criar Nova Turma de Formação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('formacao.turmas.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small">Preencha os dados da nova turma. Campos marcados com * são obrigatórios.</p>

                    <div class="row g-3">
                        {{-- Ano Letivo --}}
                        <div class="col-md-4">
                            <label for="ano_letivo" class="form-label fw-bold">Ano Letivo *</label>
                            <input type="number" name="ano_letivo" id="ano_letivo" class="form-control" 
                                placeholder="Ex: {{ date('Y') }}" 
                                value="{{ old('ano_letivo', date('Y')) }}" min="2020" max="2050" required>
                        </div>

                        {{-- Período --}}
                        <div class="col-md-4">
                            <label for="periodo" class="form-label fw-bold">Período *</label>
                            <select name="periodo" id="periodo" class="form-select" required>
                                <option value="" disabled selected>Selecione</option>
                                @foreach(['Manhã', 'Tarde', 'Integral'] as $p)
                                    <option value="{{ $p }}" {{ old('periodo') == $p ? 'selected' : '' }}>{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Letra --}}
                        <div class="col-md-4">
                            <label for="letra" class="form-label fw-bold">Letra / Identificador *</label>
                            <input type="text" name="letra" id="letra" class="form-control text-uppercase" 
                                placeholder="Ex: A, B, C, Única" value="{{ old('letra') }}" maxlength="10" required>
                        </div>

                        <hr class="mt-4 mb-3">

                        {{-- Vagas --}}
                        <div class="col-md-4">
                            <label for="vagas" class="form-label fw-bold">Número de Vagas *</label>
                            <input type="number" name="vagas" id="vagas" class="form-control" 
                                placeholder="Capacidade máxima" value="{{ old('vagas') }}" min="1" required>
                        </div>
                        
                        {{-- Professor (Assumindo que $professores está disponível na view) --}}
                        <div class="col-md-8">
                            <label for="professor_id" class="form-label fw-bold">Professor(a) Responsável (Opcional)</label>
                            <select name="professor_id" id="professor_id" class="form-select">
                                <option value="" selected>Nenhum professor atribuído</option>
                                {{--
                                    @if(isset($professores))
                                        @foreach($professores as $professor)
                                            <option value="{{ $professor->id }}" {{ old('professor_id') == $professor->id ? 'selected' : '' }}>
                                                {{ $professor->nomeCompleto }}
                                            </option>
                                        @endforeach
                                    @endif
                                --}}
                                <option value="1">Professor Teste A (Exemplo)</option>
                                <option value="2">Professor Teste B (Exemplo)</option>
                            </select>
                        </div>

                        <hr class="mt-4 mb-3">

                        {{-- Data Início --}}
                        <div class="col-md-6">
                            <label for="data_inicio" class="form-label fw-bold">Data de Início (Opcional)</label>
                            <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="{{ old('data_inicio') }}">
                        </div>

                        {{-- Data Fim --}}
                        <div class="col-md-6">
                            <label for="data_fim" class="form-label fw-bold">Data de Conclusão (Opcional)</label>
                            <input type="date" name="data_fim" id="data_fim" class="form-control" value="{{ old('data_fim') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Criar Turma</button>
                </div>
            </form>
        </div>
    </div>
</div>
