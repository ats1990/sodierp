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
        Schema::table('turmas', function (Blueprint $table) {
            // Adiciona a coluna 'is_active'.
            // O valor padrão 'true' garante que turmas criadas manualmente (que não são históricas)
            // sejam consideradas ativas, a menos que especificado o contrário.
            $table->boolean('is_active')->default(true)->after('professor_id'); 
        });
    }

    /**
     * Reverse the migrations.
     * Reverte as migrações, removendo a coluna 'is_active'.
     */
    public function down(): void
    {
        Schema::table('turmas', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};