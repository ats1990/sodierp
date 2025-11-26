<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adiciona apenas a chave estrangeira 'professor_id' à tabela 'turmas'.
     * As colunas 'vagas', 'data_inicio' e 'data_fim' foram removidas desta
     * migration porque estavam causando erros de duplicação, indicando
     * que foram criadas em migrations anteriores.
     */
    public function up(): void
    {
        if (!Schema::hasTable('turmas')) {
            return;
        }

        Schema::table('turmas', function (Blueprint $table) {
            // Verifica se a coluna 'professor_id' já existe antes de tentar criá-la
            if (!Schema::hasColumn('turmas', 'professor_id')) {
                // Chave estrangeira para o professor
                $table->foreignId('professor_id')
                    ->nullable() 
                    // Assumindo que 'data_fim' existe, usa-a como referência
                    ->after('data_fim') 
                    ->constrained('users') // Tabela de usuários
                    ->onDelete('set null') // Ação ao deletar o usuário
                    ->comment('ID do professor responsável pela turma.');
            }
        });
    }

    /**
     * Reverse the migrations.
     * Remove a chave estrangeira 'professor_id' da tabela 'turmas'.
     */
    public function down(): void
    {
        if (!Schema::hasTable('turmas')) {
            return;
        }
        
        Schema::table('turmas', function (Blueprint $table) {
            // Remove a chave estrangeira e a coluna professor_id
            if (Schema::hasColumn('turmas', 'professor_id')) {
                // DropForeign precisa usar a convenção de nome do Laravel: table_column_foreign
                // Mas o dropForeign(['column_name']) geralmente funciona bem
                $table->dropForeign(['professor_id']);
                $table->dropColumn('professor_id');
            }
            
            // As colunas vagas, data_inicio, data_fim não são removidas aqui, 
            // pois pertencem às migrations anteriores.
        });
    }
};