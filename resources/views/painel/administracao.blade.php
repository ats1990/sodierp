@extends('layouts.app')

@section('content')
    <x-sidebar />

    <div class="p-6 flex-1">
        <h2 class="text-2xl font-bold mb-4">Painel da Administração</h2>
        <div class="grid grid-cols-3 gap-6">
            <div class="bg-white p-4 rounded shadow">Usuários</div>
            <div class="bg-white p-4 rounded shadow">Configurações</div>
            <div class="bg-white p-4 rounded shadow">Relatórios</div>
        </div>
    </div>
@endsection
