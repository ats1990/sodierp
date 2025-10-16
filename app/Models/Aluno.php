<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute; // 💡 Importado para Accessors (Laravel 9+)
use App\Models\Turma;

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
        'assinatura',
        'turma_id',
    ];

    // Campos booleanos do formulário (Mantido para o Mutator)
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

    // ==========================================================
    // NOVO ACCESSOR: nome_exibicao
    // ==========================================================

    /**
     * Define o Accessor para o campo virtual 'nome_exibicao'.
     * Exibe 'Nome Completo (Nome Social)' se nomeSocial existir, ou apenas 'Nome Completo'.
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function nomeExibicao(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $nomeCompleto = $attributes['nomeCompleto'];
                $nomeSocial = $attributes['nomeSocial'];

                // Verifica se 'nomeSocial' existe e não está vazio ou é NULL
                if (!empty(trim($nomeSocial))) {
                    // Retorna: Nome Completo (Nome Social)
                    return $nomeCompleto . ' (' . $nomeSocial . ')';
                }

                // Se não houver, retorna apenas o nome completo
                return $nomeCompleto;
            },
        );
    }
    
    // ==========================================================
    // RELACIONAMENTOS
    // ==========================================================

    // Relacionamento com familiares
    public function familiares(): HasMany
    {
        return $this->hasMany(Familiar::class);
    }

    /**
     * Relação: Um Aluno pertence a uma Turma.
     */
    public function turma(): BelongsTo
    {
        return $this->belongsTo(Turma::class, 'turma_id');
    }

    // ==========================================================
    // MUTATOR (Mantido)
    // ==========================================================

    // Mutator genérico para booleanos e sanitização de CPF
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
