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

            // 1- Dados pessoais
            $table->string('nomeCompleto');
            $table->string('nomeSocial')->nullable();
            $table->date('dataNascimento');
            $table->integer('idade');
            $table->string('cpf');
            $table->string('rg');
            $table->string('carteiraTrabalho')->nullable();
            $table->string('jaTrabalhou')->nullable();
            $table->string('ctpsAssinada')->nullable();
            $table->string('qualFuncao')->nullable();

            // 2- Endereço
            $table->string('ruaAv');
            $table->string('numero')->nullable();
            $table->string('complemento')->nullable();
            $table->string('cep');
            $table->string('cidade');
            $table->string('bairro');
            $table->string('uf');
            $table->string('tel')->nullable();
            $table->string('cel');
            $table->string('rec')->nullable();
            $table->string('email')->nullable();

            // 3- Escolaridade
            $table->string('escola')->nullable();
            $table->string('anoEscolar')->nullable();
            $table->string('periodo')->nullable();
            $table->string('concluidoEscolaridade')->nullable();
            $table->integer('anoConclusao')->nullable();
            $table->string('cursoAtual')->nullable();

            // 4- Dados socioeconômicos
            $table->string('moradia')->nullable();
            $table->string('quemCedida')->nullable();
            $table->string('recebeBeneficio')->nullable();
            $table->string('bolsaFamiliaValor')->nullable();
            $table->string('bpcLoasValor')->nullable();
            $table->string('pensaoAlimenticiaValor')->nullable();
            $table->string('auxAluguelValor')->nullable();
            $table->string('rendaCidadaValor')->nullable();
            $table->string('outrosBeneficiosValor')->nullable();

            // 5- Despesas mensais
            $table->string('aguaDespesa')->nullable();
            $table->string('alimentacaoDespesa')->nullable();
            $table->string('gasDespesa')->nullable();
            $table->string('luzDespesa')->nullable();
            $table->string('medicamentoDespesa')->nullable();
            $table->string('telefoneInternetDespesa')->nullable();
            $table->string('aluguelFinanciamentoDespesa')->nullable();

            // 5.1- Família (pode ter várias linhas)
            $table->json('familia')->nullable(); // armazenar parentesco, nome, idade, profissão, empresa, salário

            // 6- Saúde
            $table->string('ubsMatriculado')->nullable();
            $table->string('convenioMedico')->nullable();
            $table->string('qualConvenio')->nullable();
            $table->string('vacinacaoEmDia')->nullable();
            $table->string('queixaSaude')->nullable();
            $table->string('qualQueixa')->nullable();
            $table->string('possuiAlergia')->nullable();
            $table->string('qualAlergia')->nullable();
            $table->string('fezTratamento')->nullable();
            $table->string('qualTratamento')->nullable();
            $table->string('usaRemedio')->nullable();
            $table->string('qualRemedio')->nullable();
            $table->string('fezCirurgia')->nullable();
            $table->string('motivoCirurgia')->nullable();
            $table->string('isPCD')->nullable();
            $table->string('qualDeficiencia')->nullable();
            $table->string('qualNecessidade')->nullable();
            $table->string('doencaCongenita')->nullable();
            $table->string('qualDoencaCongenita')->nullable();
            $table->string('passouPsicologo')->nullable();
            $table->string('quandoPsicologo')->nullable();
            $table->string('teveConvulsoes')->nullable();
            $table->string('quandoConvulsoes')->nullable();
            $table->string('familiarDoencaCongenita')->nullable();
            $table->string('quemDoencaCongenita')->nullable();
            $table->string('familiarMedicamentoDepressao')->nullable();
            $table->string('quemMedicamentoDepressao')->nullable();
            $table->string('passaMedicoEspecialista')->nullable();
            $table->string('qualMedicoEspecialista')->nullable();
            $table->string('familiarPsicologo')->nullable();
            $table->string('quemFamiliarPsicologo')->nullable();
            $table->string('usoAbusivoAlcool')->nullable();
            $table->string('quemAlcoolismo')->nullable();
            $table->string('usoAbusivoDrogas')->nullable();
            $table->string('quemDrogas')->nullable();

            // assinatura e data
            $table->text('assinatura')->nullable();
            $table->string('dataDia')->nullable();
            $table->string('dataMes')->nullable();
            $table->string('dataAno')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alunos');
    }
};
