<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Necessário para DB::statement

return new class extends Migration
{
    /**
     * Executa a migration: Corrige o Charset e Collation das tabelas para utf8mb4.
     */
    public function up(): void
    {
        // Comando SQL para corrigir a tabela alunos
        DB::statement("ALTER TABLE alunos CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        // Comando SQL para corrigir a tabela turmas
        DB::statement("ALTER TABLE turmas CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }

    /**
     * Reverte a migration.
     * Reverter esta correção geralmente não é desejado, pois pode quebrar caracteres
     * especiais. Deixamos o método down() vazio ou optamos por uma reversão segura.
     */
    public function down(): void
    {
        // Não é recomendado reverter esta correção, mas para completude:
        // DB::statement("ALTER TABLE alunos CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci");
        // DB::statement("ALTER TABLE turmas CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci");
    }
};