{{-- resources/views/painel/coordenacao.blade.php --}}
@extends('layouts.app')

@section('title', 'Painel Coordenação')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

    {{-- Card 1 --}}
    <div class="bg-white p-6 rounded-2xl shadow hover:shadow-lg transition cursor-pointer">
        <h2 class="text-xl font-semibold text-gray-800 mb-2">Gerenciar Usuários</h2>
        <p class="text-gray-600 mb-4">Ativar contas, editar perfis e gerenciar permissões.</p>
        <a href="{{ route('usuarios.create') }}"
           class="inline-block px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-800">Acessar</a>
    </div>

    {{-- Card 2 --}}
    <div class="bg-white p-6 rounded-2xl shadow hover:shadow-lg transition cursor-pointer">
        <h2 class="text-xl font-semibold text-gray-800 mb-2">Relatórios</h2>
        <p class="text-gray-600 mb-4">Visualize relatórios de presença, avaliações e desempenho.</p>
        <a href="#"
           class="inline-block px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-800">Acessar</a>
    </div>

    {{-- Card 3 --}}
    <div class="bg-white p-6 rounded-2xl shadow hover:shadow-lg transition cursor-pointer">
        <h2 class="text-xl font-semibold text-gray-800 mb-2">Turmas</h2>
        <p class="text-gray-600 mb-4">Gerencie turmas, professores e horários.</p>
        <a href="{{ route('aluno.index') }}"
           class="inline-block px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-800">Acessar</a>
    </div>

</div>
@endsection
