@extends('layouts.app') 

@section('title', 'Gerenciamento de Usuários')

@section('content')

<div class="page-header">
    <h3 class="page-title"> Gerenciamento de Usuários </h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('painel.coordenacao') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Usuários</li>
        </ol>
    </nav>
</div>

{{-- Exibir mensagens de sucesso ou erro --}}
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Lista de Usuários Cadastrados</h4>
                <p class="card-description"> Gerencie o status de ativação dos usuários do sistema.</p>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nome Completo</th>
                                <th>E-mail</th>
                                <th>Tipo</th>
                                <th>Status Atual</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($usuarios as $usuario)
                                <tr>
                                    <td>{{ $usuario->nomeCompleto }}</td>
                                    <td>{{ $usuario->email }}</td>
                                    <td>{{ ucfirst($usuario->tipo) }}</td>
                                    <td>
                                        {{-- Exibe o status com uma badge colorida --}}
                                        @if ($usuario->status === 'ativo')
                                            <label class="badge badge-success">Ativo</label>
                                        @else
                                            <label class="badge badge-danger">Inativo</label>
                                        @endif
                                    </td>
                                    <td>
                                        {{-- Botão de Ação: Ativar ou Desativar --}}
                                        @if ($usuario->status === 'inativo')
                                            {{-- Se inativo, mostra o botão ATIVAR --}}
                                            <form action="{{ route('usuarios.ativar', $usuario->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('PATCH') 
                                                <button type="submit" class="btn btn-sm btn-outline-success">
                                                    Ativar
                                                </button>
                                            </form>
                                        @else
                                            {{-- Se ativo, mostra o botão DESATIVAR --}}
                                            <form action="{{ route('usuarios.desativar', $usuario->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    Desativar
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Nenhum usuário encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection