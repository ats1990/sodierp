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
<livewire:gerenciar-usuarios />

@endsection