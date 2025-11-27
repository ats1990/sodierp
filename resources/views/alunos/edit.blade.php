@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 style="color: black !important;">Editar Aluno: {{ $aluno->nomeCompleto }}</h1>
            <p class="lead">ID: {{ $aluno->id }} | Turma: {{ $aluno->turma->nomeCompleto ?? 'Não Atribuída' }}</p>
        </div>
    </div>

    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <ul class="nav nav-tabs mb-4" id="alunoTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="dados-tab" data-toggle="tab" href="#dados" role="tab" aria-controls="dados" aria-selected="true">Dados Principais</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="familiares-tab" data-toggle="tab" href="#familiares" role="tab" aria-controls="familiares" aria-selected="false">Composição Familiar ({{ $aluno->familiares->count() }})</a>
        </li>
        {{-- Adicione outras abas aqui, como Endereço, Histórico, etc. --}}
    </ul>

    <div class="tab-content" id="alunoTabContent">

        <div class="tab-pane fade show active" id="dados" role="tabpanel" aria-labelledby="dados-tab">

            <form action="{{ route('aluno.update', $aluno) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card p-4 shadow-sm">
                    <h3>Informações Pessoais</h3>
                    <hr>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="nomeCompleto">Nome Completo</label>
                            <input type="text" class="form-control @error('nomeCompleto') is-invalid @enderror" id="nomeCompleto" name="nomeCompleto" value="{{ old('nomeCompleto', $aluno->nomeCompleto) }}" required>
                            @error('nomeCompleto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group col-md-3">
                            <label for="cpf">CPF</label>
                            <input type="text" class="form-control @error('cpf') is-invalid @enderror" id="cpf" name="cpf" value="{{ old('cpf', $aluno->cpf) }}" placeholder="Ex: 123.456.789-00">
                            @error('cpf') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group col-md-3">
                            <label for="dataNascimento">Data de Nasc. (DD/MM/AAAA)</label>
                            <input type="text" class="form-control @error('dataNascimento') is-invalid @enderror" id="dataNascimento" name="dataNascimento" value="{{ old('dataNascimento', optional($aluno->dataNascimento)->format('d/m/Y')) }}" required>
                            @error('dataNascimento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="turma_id">Turma Atual</label>
                            <select id="turma_id" name="turma_id" class="form-control @error('turma_id') is-invalid @enderror">
                                <option value="">Nenhuma Turma</option>
                                @foreach ($turmas as $id => $nome)
                                <option value="{{ $id }}" {{ old('turma_id', $aluno->turma_id) == $id ? 'selected' : '' }}>
                                    {{ $nome }}
                                </option>
                                @endforeach
                            </select>
                            @error('turma_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group col-md-4">
                            <label for="celular">Celular</label>
                            <input type="text" class="form-control" id="celular" name="celular" value="{{ old('celular', $aluno->celular) }}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="email">E-mail</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $aluno->email) }}">
                        </div>
                    </div>

                    {{-- Outros campos importantes do aluno aqui (Endereço, Histórico Escolar, etc.) --}}

                    <div class="form-group">
                        <label for="observacoes">Observações</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="3">{{ old('observacoes', $aluno->observacoes) }}</textarea>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">Salvar Alterações do Aluno</button>
                    <a href="{{ route('aluno.show', $aluno) }}" class="btn btn-secondary btn-lg">Voltar ao Perfil</a>
                </div>
            </form>

        </div>

        <div class="tab-pane fade" id="familiares" role="tabpanel" aria-labelledby="familiares-tab">
            <div class="card p-4 shadow-sm">
                <h3>Adicionar Novo Familiar</h3>
                <hr>

                <form action="{{ route('familiar.store', $aluno) }}" method="POST">
                    @csrf
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="nomeCompleto_familiar">Nome Completo</label>
                            <input type="text" class="form-control @error('nomeCompleto_familiar') is-invalid @enderror" id="nomeCompleto_familiar" name="nomeCompleto" value="{{ old('nomeCompleto') }}" required>
                            @error('nomeCompleto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group col-md-2">
                            <label for="parentesco">Parentesco</label>
                            <input type="text" class="form-control @error('parentesco') is-invalid @enderror" id="parentesco" name="parentesco" value="{{ old('parentesco') }}" required>
                            @error('parentesco') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group col-md-1">
                            <label for="idade_familiar">Idade</label>
                            <input type="number" class="form-control @error('idade') is-invalid @enderror" id="idade_familiar" name="idade" value="{{ old('idade') }}" min="0">
                            @error('idade') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group col-md-3">
                            <label for="profissao">Profissão/Empresa</label>
                            <input type="text" class="form-control" id="profissao" name="profissao" value="{{ old('profissao') }}" placeholder="Ex: Mãe, Auxiliar de Limpeza">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="salarioBase">Renda Base (Ex: 1.500,00)</label>
                            <input type="text" class="form-control @error('salarioBase') is-invalid @enderror" id="salarioBase" name="salarioBase" value="{{ old('salarioBase') }}">
                            @error('salarioBase') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success">Adicionar Familiar</button>
                </form>

                <h4 class="mt-5">Familiares Registrados</h4>
                <hr>

                @if($aluno->familiares->isEmpty())
                <p class="text-info">Nenhum familiar cadastrado para este aluno.</p>
                @else
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Parentesco</th>
                            <th>Idade</th>
                            <th>Profissão</th>
                            <th>Renda Base</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($aluno->familiares as $familiar)
                        <tr>
                            <td>{{ $familiar->nomeCompleto }}</td>
                            <td>{{ $familiar->parentesco }}</td>
                            <td>{{ $familiar->idade ?? '--' }}</td>
                            <td>{{ $familiar->profissao ?? '--' }}</td>
                            <td>R$ {{ number_format($familiar->salarioBase, 2, ',', '.') }}</td>
                            <td>
                                <form action="{{ route('familiar.destroy', $familiar) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja remover este familiar?')">
                                        Remover
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection

{{-- Script JS para a funcionalidade das abas (se estiver usando Bootstrap 4) --}}
@push('scripts')
<script>
    $(document).ready(function() {
        // Ativa o toggle da aba
        $('#alunoTab a').on('click', function(e) {
            e.preventDefault()
            $(this).tab('show')
        })

        // Mantém a aba ativa após um refresh (útil para erros de validação)
        var hash = window.location.hash;
        if (hash) {
            $('#alunoTab a[href="' + hash + '"]').tab('show');
        }
    });
</script>
@endpush