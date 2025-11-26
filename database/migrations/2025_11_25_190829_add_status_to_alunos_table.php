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
        // 1. Verifica se a tabela 'alunos' existe (por segurança)
        if (Schema::hasTable('alunos')) {
            
            // 2. Adiciona a coluna 'status'
            Schema::table('alunos', function (Blueprint $table) {
                
                // Usaremos uma string de 100 caracteres, permitindo valores nulos, 
                // e a colocaremos após a coluna 'email', por exemplo.
                // Ajuste 'after()' se quiser posicioná-la em outro lugar.
                if (!Schema::hasColumn('alunos', 'status')) {
                    $table->string('status', 100)->nullable()->after('email');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Verifica se a tabela 'alunos' existe
        if (Schema::hasTable('alunos')) {

            // 2. Remove a coluna 'status'
            Schema::table('alunos', function (Blueprint $table) {
                
                // Garante que a coluna existe antes de tentar removê-la
                if (Schema::hasColumn('alunos', 'status')) {
                    $table->dropColumn('status');
                }
            });
        }
    }
};