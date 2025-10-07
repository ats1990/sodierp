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
        // Cria a tabela 'turmas'
        Schema::create('turmas', function (Blueprint $table) {
            $table->id();
            
            $table->string('periodo'); // Manhã, Tarde, Noite
            $table->char('letra', 1); // A, B, C...
            $table->year('ano_letivo'); // Ex: 2025
            $table->unsignedSmallInteger('vagas')->default(32);
            
            // Novos campos para definir o calendário letivo
            $table->date('data_inicio')->nullable(); 
            $table->date('data_fim')->nullable(); 
            
            // Relacionamento com Professor (assumindo a tabela 'users' para o foreign key)
            $table->foreignId('professor_id')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            
            // Restrição de unicidade para garantir que não haja duas turmas com o mesmo período/letra/ano
            $table->unique(['periodo', 'letra', 'ano_letivo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('turmas');
    }
};
