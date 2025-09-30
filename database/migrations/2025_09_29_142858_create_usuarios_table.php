<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nomeCompleto');
            $table->string('nomeSocial')->nullable();
            $table->string('email')->unique();
            $table->string('cpf')->nullable()->unique();
            $table->string('password');
            $table->enum('tipo', ['professor','coordenacao','administracao','psicologo']);
           $table->enum('status', ['ativo','inativo'])->default('inativo');


            // Programas como boolean
            $table->boolean('programa_basica')->default(false);
            $table->boolean('programa_aprendizagem')->default(false);
            $table->boolean('programa_convivencia')->default(false);

            // Disciplinas como JSON
            $table->json('disciplinas_basica')->nullable();
            $table->json('disciplinas_aprendizagem')->nullable();
            $table->json('disciplinas_convivencia')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
