<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('familiares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aluno_id')->constrained('alunos')->onDelete('cascade');
            $table->string('parentesco')->nullable();
            $table->string('nomeCompleto');
            $table->integer('idade')->nullable();
            $table->string('profissao')->nullable();
            $table->string('empresa')->nullable();
            $table->decimal('salarioBase', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('familiares');
    }
};
