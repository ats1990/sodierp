<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Adicionado para tipagem correta
use App\Models\Turma; // <-- Importado para o relacionamento

class Aluno extends Model
{
    use HasFactory;

    protected $table = 'alunos';

    protected $fillable = [
        'nomeCompleto',
        'nomeSocial',
        'dataNascimento',
        'idade',
        'cpf',
        'rg',
        'mao_dominante',
        'carteiraTrabalho',
        'jaTrabalhou',
        'ctpsAssinada',
        'qualFuncao',
        'cep',
        'rua',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'uf',
        'telefone',
        'celular',
        'email',
        'escola',
        'ano',
        'periodo',
        'concluido',
        'anoConclusao',
        'cursoAtual',
        'moradia',
        'moradia_porquem',
        'beneficio',
        'bolsa_familia',
        'bpc_loas',
        'pensao',
        'aux_aluguel',
        'renda_cidada',
        'outros',
        'observacoes',
        'agua',
        'alimentacao',
        'gas',
        'luz',
        'medicamento',
        'telefone_internet',
        'aluguel_financiamento',
        'ubs', // Campo novo
        'convenio', // Campo novo
        'qual_convenio', // Campo novo
        'vacinacao', // Campo novo
        'queixa_saude', // Campo novo
        'qual_queixa', // Campo novo
        'alergia', // Campo novo
        'qual_alergia', // Campo novo
        'tratamento', // Campo novo
        'qual_tratamento', // Campo novo
        'uso_remedio', // Campo novo
        'qual_remedio', // Campo novo
        'cirurgia', // Campo novo
        'motivo_cirurgia', // Campo novo
        'pcd', // Campo novo
        'qual_pcd', // Campo novo
        'necessidade_especial', // Campo novo
        'doenca_congenita', // Campo novo
        'qual_doenca_congenita', // Campo novo
        'psicologo', // Campo novo
        'quando_psicologo', // Campo novo
        'convulsao', // Campo novo
        'quando_convulsao', // Campo novo
        'familia_doenca', // Campo novo
        'qual_familia_doenca', // Campo novo
        'familia_depressao', // Campo novo
        'quem_familia_depressao', // Campo novo
        'medico_especialista', // Campo novo
        'qual_medico_especialista', // Campo novo
        'familia_psicologico', // Campo novo
        'quem_familia_psicologico', // Campo novo
        'familia_alcool', // Campo novo
        'quem_familia_alcool', // Campo novo
        'familia_drogas', // Campo novo
        'quem_familia_drogas', // Campo novo
        'declaracao_consentimento',
        'assinatura', // Campo novo
        'turma_id', // <-- CRUCIAL: Adicionado para permitir atribuir o aluno a uma turma
    ];

    // Campos booleanos do formulário
    protected array $booleanFields = [
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
        'declaracao_consentimento'
    ];

    // Relacionamento com familiares
    public function familiares(): HasMany
    {
        return $this->hasMany(Familiar::class);
    }

    /**
     * Relação: Um Aluno pertence a uma Turma.
     * Adicionado o relacionamento de volta.
     */
    public function turma()
    {
        return $this->belongsTo(Turma::class, 'turma_id');
    }


    // Mutator genérico para booleanos
    protected static function booted()
    {
        static::saving(function ($aluno) {
            foreach ($aluno->booleanFields as $field) {
                if (isset($aluno->$field)) {
                    // Garante que 'sim'/'on' se tornem true (1 no banco) e outros se tornem false (0)
                    $aluno->$field = in_array($aluno->$field, ['sim', 'on', true, 1], true);
                }
            }

            // Sanitiza CPF (remove todos os caracteres que não são números)
            if (isset($aluno->cpf)) {
                $aluno->cpf = preg_replace('/[^0-9]/', '', $aluno->cpf);
            }
        });
    }
}
