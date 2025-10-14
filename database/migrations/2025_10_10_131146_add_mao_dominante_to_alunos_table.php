<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('alunos', function (Blueprint $table) {
            // Adiciona um campo ENUM que só aceita 'destro' ou 'canhoto'.
            // O '.nullable()' permite que o campo fique vazio inicialmente para alunos já existentes.
            // O '.after("rg")' posiciona o campo após a coluna 'rg' (baseado na estrutura que você forneceu).
            $table->enum('mao_dominante', ['destro', 'canhoto'])->nullable()->after('rg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alunos', function (Blueprint $table) {
            // Este método é essencial para reverter a migration (rollback),
            // garantindo que a coluna seja removida.
            $table->dropColumn('mao_dominante');
        });
    }
};