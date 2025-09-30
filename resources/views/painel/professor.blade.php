@extends('layouts.app')

@section('content')
<div class="p-6 max-w-7xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Painel do Professor</h1>

    <div class="bg-white rounded-xl shadow p-4">
        <h2 class="text-xl font-semibold mb-4">Seus Alunos</h2>
        @if($alunos->isEmpty())
            <p class="text-gray-600">Você ainda não possui alunos cadastrados.</p>
        @else
            <ul class="divide-y divide-gray-200">
                @foreach($alunos as $aluno)
                    <li class="py-2 flex justify-between items-center">
                        <span>{{ $aluno->nomeCompleto }}</span>
                        <span class="text-gray-500">{{ $aluno->status }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection