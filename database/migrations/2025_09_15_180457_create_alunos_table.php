<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alunos', function (Blueprint $table) {
            $table->id();

            // STEP 1: DADOS PESSOAIS
            $table->string('nomeCompleto');
            $table->string('nomeSocial')->nullable();
            $table->date('dataNascimento');
            $table->integer('idade')->nullable();
            $table->string('cpf', 14)->nullable();
            $table->string('rg', 20)->nullable();
            $table->boolean('carteiraTrabalho')->default(false);
            $table->boolean('jaTrabalhou')->default(false);
            $table->boolean('ctpsAssinada')->default(false);
            $table->string('qualFuncao')->nullable();

            // STEP 2: ENDEREÇO
            $table->string('cep', 10)->nullable();
            $table->string('rua')->nullable();
            $table->string('numero', 10)->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('uf', 2)->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('celular', 20)->nullable();
            $table->string('email')->nullable();

            // STEP 3: ESCOLARIDADE
            $table->string('escola')->nullable();
            $table->string('ano', 5)->nullable();
            $table->string('periodo')->nullable();
            $table->boolean('concluido')->default(false);
            $table->string('anoConclusao', 4)->nullable();
            $table->string('cursoAtual')->nullable();

            // STEP 4: DADOS SOCIOECONÔMICOS
            $table->string('moradia')->nullable();
            $table->string('moradia_porquem')->nullable();
            $table->boolean('beneficio')->default(false);
            $table->decimal('bolsa_familia', 10, 2)->nullable();
            $table->decimal('bpc_loas', 10, 2)->nullable();
            $table->decimal('pensao', 10, 2)->nullable();
            $table->decimal('aux_aluguel', 10, 2)->nullable();
            $table->decimal('renda_cidada', 10, 2)->nullable();
            $table->decimal('outros', 10, 2)->nullable();
            $table->decimal('agua', 10, 2)->nullable();
            $table->decimal('alimentacao', 10, 2)->nullable();
            $table->decimal('gas', 10, 2)->nullable();
            $table->decimal('luz', 10, 2)->nullable();
            $table->decimal('medicamento', 10, 2)->nullable();
            $table->decimal('telefone_internet', 10, 2)->nullable();
            $table->decimal('aluguel_financiamento', 10, 2)->nullable();
            $table->text('observacoes')->nullable();

            // STEP 5: SAÚDE
            $table->string('ubs')->nullable();
            $table->boolean('convenio')->default(false);
            $table->string('qual_convenio')->nullable();
            $table->boolean('vacinacao')->default(false);
            $table->boolean('queixa_saude')->default(false);
            $table->string('qual_queixa')->nullable();
            $table->boolean('alergia')->default(false);
            $table->string('qual_alergia')->nullable();
            $table->boolean('tratamento')->default(false);
            $table->string('qual_tratamento')->nullable();
            $table->boolean('uso_remedio')->default(false);
            $table->string('qual_remedio')->nullable();
            $table->boolean('cirurgia')->default(false);
            $table->string('motivo_cirurgia')->nullable();
            $table->boolean('pcd')->default(false);
            $table->string('qual_pcd')->nullable();
            $table->string('necessidade_especial')->nullable();
            $table->boolean('doenca_congenita')->default(false);
            $table->string('qual_doenca_congenita')->nullable();
            $table->boolean('psicologo')->default(false);
            $table->string('quando_psicologo')->nullable();
            $table->boolean('convulsao')->default(false);
            $table->string('quando_convulsao')->nullable();
            $table->boolean('familia_doenca')->default(false);
            $table->string('qual_familia_doenca')->nullable();
            $table->boolean('familia_depressao')->default(false);
            $table->string('quem_familia_depressao')->nullable();
            $table->boolean('medico_especialista')->default(false);
            $table->string('qual_medico_especialista')->nullable();
            $table->boolean('familia_psicologico')->default(false);
            $table->string('quem_familia_psicologico')->nullable();
            $table->boolean('familia_alcool')->default(false);
            $table->string('quem_familia_alcool')->nullable();
            $table->boolean('familia_drogas')->default(false);
            $table->string('quem_familia_drogas')->nullable();

            // STEP 6: DECLARAÇÃO E CONSENTIMENTO
            $table->boolean('declaracao_consentimento')->default(false);

            // STEP 7: ASSINATURA
            $table->text('assinatura')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alunos');
    }
};
