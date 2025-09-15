<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aluno extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomeCompleto', 'nomeSocial', 'dataNascimento', 'idade', 'cpf', 'rg',
        'carteiraTrabalho', 'jaTrabalhou', 'ctpsAssinada', 'qualFuncao',
        'ruaAv', 'numero', 'complemento', 'cep', 'cidade', 'bairro', 'uf',
        'tel', 'cel', 'rec', 'email',
        'escola', 'anoEscolar', 'periodo', 'concluidoEscolaridade', 'anoConclusao', 'cursoAtual',
        'moradia', 'quemCedida', 'recebeBeneficio', 'beneficios', 
        'aguaDespesa','alimentacaoDespesa','gasDespesa','luzDespesa','medicamentoDespesa','telefoneInternetDespesa','aluguelFinanciamentoDespesa',
        'ubsMatriculado', 'convenioMedico', 'qualConvenio', 'vacinacaoEmDia',
        'queixaSaude', 'qualQueixa', 'possuiAlergia', 'qualAlergia', 'fezTratamento', 'qualTratamento',
        'usaRemedio', 'qualRemedio', 'fezCirurgia', 'motivoCirurgia', 'isPCD', 'qualDeficiencia',
        'qualNecessidade', 'doencaCongenita', 'qualDoencaCongenita', 'passouPsicologo', 'quandoPsicologo',
        'teveConvulsoes', 'quandoConvulsoes', 'familiarDoencaCongenita', 'quemDoencaCongenita',
        'familiarMedicamentoDepressao', 'quemMedicamentoDepressao', 'passaMedicoEspecialista', 'qualMedicoEspecialista',
        'familiarPsicologo', 'quemFamiliarPsicologo', 'usoAbusivoAlcool', 'quemAlcoolismo',
        'usoAbusivoDrogas', 'quemDrogas', 'assinatura', 'dataAssinatura'
    ];

    protected $casts = [
        'beneficios' => 'array',
    ];
}
