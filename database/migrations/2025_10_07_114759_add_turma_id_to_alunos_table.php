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
            // Adiciona a chave estrangeira 'turma_id'
            $table->foreignId('turma_id')
                  ->nullable() // Permite que o aluno exista antes de ser vinculado
                  ->after('email') // Posição arbitrária, ajuste se quiser
                  ->constrained('turmas') // Referencia a nova tabela 'turmas'
                  ->onDelete('set null'); // Mantém o aluno se a turma for apagada
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alunos', function (Blueprint $table) {
            // Remove a chave estrangeira de forma segura
            $table->dropForeign(['turma_id']);
            $table->dropColumn('turma_id');
        });
    }
};