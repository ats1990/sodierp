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

{{-- 🚨 IMPORTANTE: As mensagens de sessão (success/error) já são tratadas DENTRO do componente Livewire para que funcionem corretamente. Você pode REMOVÊ-LAS daqui. 🚨 --}}

{{-- 
    🚨 AQUI ESTÁ A ÚNICA COISA QUE PRECISA FICAR! 🚨
    Este componente carrega toda a lógica e o HTML da tabela de usuários que você estava usando, 
    agora com a funcionalidade de edição in-line.
--}}
<livewire:gerenciar-usuarios />

@endsection