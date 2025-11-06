@extends('layouts.app') {{-- Assumindo que este é o seu layout principal --}}

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="mdi mdi-upload me-2"></i> Importação de Dados de Alunos via CSV</h4>
                </div>

                <div class="card-body">
                    
                    {{-- 1. EXIBIÇÃO DE MENSAGENS DE SESSÃO --}}
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    {{-- 2. EXIBIÇÃO DOS ERROS DE VALIDAÇÃO LINHA POR LINHA --}}
                    @if(session('import_errors'))
                        <div class="alert alert-warning">
                            <h5 class="alert-heading">Atenção: Erros de Importação</h5>
                            <p>Foram encontrados erros nas seguintes linhas. Apenas as linhas sem erros foram importadas com sucesso:</p>
                            
                            {{-- Container para permitir rolagem se a lista de erros for muito longa --}}
                            <div style="max-height: 250px; overflow-y: scroll; border: 1px solid #dee2e6; padding: 10px; background-color: #fff;">
                                <ul class="mb-0">
                                    @foreach (session('import_errors') as $error)
                                        <li class="small text-danger">{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                    
                    {{-- 3. EXIBIÇÃO DE ERROS DO FORMULÁRIO (Ex: Arquivo Inválido) --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <p class="mb-0">Corrija o(s) seguinte(s) erro(s) do formulário:</p>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <p class="mt-4">
                        **Instruções:** Selecione o arquivo CSV (Comma Separated Values) contendo os dados dos alunos. 
                        Certifique-se de que o arquivo utiliza o **ponto e vírgula** (`;`) como delimitador.
                    </p>
                    <p class="text-info">
                        <i class="mdi mdi-alert-circle-outline"></i> Os cabeçalhos de coluna no CSV devem ser **exatos** para que o mapeamento funcione.
                    </p>

                    <hr>

                    {{-- 4. O FORMULÁRIO DE UPLOAD --}}
                    <form action="{{ route('aluno.import.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="form-group mb-4">
                            <label for="csv_file" class="form-label fw-bold">Selecione o Arquivo CSV:</label>
                            <input type="file" name="csv_file" id="csv_file" class="form-control form-control-lg" accept=".csv, .txt" required>
                            <div class="form-text">Apenas arquivos .csv ou .txt são aceitos (máx. 10MB).</div>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="mdi mdi-cloud-upload me-2"></i> Iniciar Importação
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection