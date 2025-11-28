<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Aluno extends Model
{
    use HasFactory;

    protected $table = 'alunos';

    protected $fillable = [
        'codigo_matricula',
        'nomeCompleto',
        'nomeSocial',
        'dataNascimento',
        'cpf',
        'rg',
        'mao_dominante',

        // Trabalho (nomes corretos do banco)
        'carteiraTrabalho',
        'jaTrabalhou',
        'ctpsAssinada',
        'qualFuncao',

        // Endereço
        'cep',
        'rua',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'uf',

        // Contatos
        'telefone',
        'celular',
        'email',

        // Escolaridade
        'escola',
        'ano',
        'periodo',
        'concluido',
        'anoConclusao',
        'cursoAtual',

        // Socioeconômico
        'moradia',
        'moradia_porquem',
        'beneficio',
        'bolsa_familia',
        'bpc_loas',
        'pensao',
        'aux_aluguel',
        'renda_cidada',
        'outros',
        'agua',
        'alimentacao',
        'gas',
        'luz',
        'medicamento',
        'telefone_internet',
        'aluguel_financiamento',

        'observacoes',

        // Saúde
        'ubs',
        'convenio',
        'qual_convenio',
        'vacinacao',
        'queixa_saude',
        'qual_queixa',
        'alergia',
        'qual_alergia',
        'tratamento',
        'qual_tratamento',
        'uso_remedio',
        'qual_remedio',
        'cirurgia',
        'motivo_cirurgia',
        'pcd',
        'qual_pcd',
        'necessidade_especial',
        'doenca_congenita',
        'qual_doenca_congenita',
        'psicologo',
        'quando_psicologo',
        'convulsao',
        'quando_convulsao',
        'familia_doenca',
        'qual_familia_doenca',
        'familia_depressao',
        'quem_familia_depressao',
        'medico_especialista',
        'qual_medico_especialista',
        'familia_psicologico',
        'quem_familia_psicologico',
        'familia_alcool',
        'quem_familia_alcool',
        'familia_drogas',
        'quem_familia_drogas',

        'declaracao_consentimento',

        'turma_id',
        'user_id',
        'assinatura',
    ];

    protected $booleanFields = [
        'carteiraTrabalho',
        'jaTrabalhou',
        'ctpsAssinada',
        'concluido',
        'beneficio',
        'convenio',
        'vacinacao',
        'queixa_saude',
        'alergia',
        'tratamento',
        'uso_remedio',
        'cirurgia',
        'pcd',
        'doenca_congenita',
        'psicologo',
        'convulsao',
        'familia_doenca',
        'familia_depressao',
        'medico_especialista',
        'familia_psicologico',
        'familia_alcool',
        'familia_drogas',
        'declaracao_consentimento',
    ];

    protected static function booted()
    {
        static::saving(function ($aluno) {

            // Convertendo booleanos corretamente
            foreach ($aluno->booleanFields as $field) {
                if (isset($aluno->$field)) {
                    $aluno->$field = in_array($aluno->$field, ['on', '1', 1, true, 'sim'], true) ? 1 : 0;
                }
            }

            // Sanitização CPF
            if (isset($aluno->cpf)) {
                $aluno->cpf = preg_replace('/[^0-9]/', '', $aluno->cpf);
            }

            // Sanitização RG
            if (isset($aluno->rg)) {
                $aluno->rg = preg_replace('/[^a-zA-Z0-9]/', '', $aluno->rg);
            }
        });
    }

    public function idade(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) =>
                isset($attributes['dataNascimento'])
                    ? Carbon::parse($attributes['dataNascimento'])->age
                    : null
        );
    }

    public function turma(): BelongsTo
    {
        return $this->belongsTo(Turma::class, 'turma_id');
    }

    public function familiares(): HasMany
    {
        return $this->hasMany(Familiar::class);
    }
}