<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alunos', function (Blueprint $table) {
            // A CHAVE CRÍTICA: Deve ser string e UNICA (para o updateOrCreate funcionar)
            $table->string('codigo_matricula', 100)
                  ->unique()
                  ->nullable() // Permite nulos, mas é altamente recomendado que não sejam.
                  ->after('id'); // Posição após o ID, por exemplo.
        });
    }

    public function down(): void
    {
        Schema::table('alunos', function (Blueprint $table) {
            // Remove o índice UNIQUE antes de apagar a coluna
            $table->dropUnique(['codigo_matricula']); 
            $table->dropColumn('codigo_matricula');
        });
    }
};