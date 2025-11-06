<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute; 
// 🚨 CORREÇÃO ESSENCIAL: Adicionada importação da classe Carbon
use Carbon\Carbon; 
use App\Models\Turma;
use App\Models\Familiar; 
use App\Models\Presenca; 

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
        'aux_emergencial',
        'outros_beneficios',
        'situacao_trabalho_pai',
        'situacao_trabalho_mae',
        'renda_familiar',
        'qtd_membros_familia',
        'turma_id',
        'observacoes',
        'user_id', 
    ];
    
    // Campos que devem ser tratados como booleanos no mutator (mesmo que venham como string)
    protected $booleanFields = [
        'jaTrabalhou',
        'ctpsAssinada',
        'concluido',
        'beneficio',
        'bolsa_familia', 
        'bpc_loas',
        'pensao',
        'aux_emergencial',
    ];


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

    /**
     * Relação: Um Aluno tem muitas Presenças.
     */
    public function presencas(): HasMany
    {
        return $this->hasMany(Presenca::class, 'aluno_id');
    }

    // ==========================================================
    // MUTATOR
    // ==========================================================

    // Mutator genérico para booleanos e sanitização de CPF
    protected static function booted()
    {
        static::saving(function ($aluno) {
            foreach ($aluno->booleanFields as $field) {
                if (isset($aluno->$field)) {
                    $aluno->$field = in_array($aluno->$field, ['sim', 'on', true, 1], true) ? 1 : 0;
                }
            }

            // Sanitiza CPF (remove todos os caracteres que não são números)
            if (isset($aluno->cpf)) {
                $aluno->cpf = preg_replace('/[^0-9]/', '', $aluno->cpf);
            }
             // Sanitiza RG (remove todos os caracteres que não são alfanuméricos)
            if (isset($aluno->rg)) {
                $aluno->rg = preg_replace('/[^a-zA-Z0-9]/', '', $aluno->rg);
            }
        });
    }
    
    // ==========================================================
    // ACCESSORS 
    // ==========================================================

    /**
     * Accessor para retornar a idade do aluno a partir da data de nascimento.
     */
    protected function idade(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => 
                isset($attributes['dataNascimento']) 
                ? Carbon::parse($attributes['dataNascimento'])->age 
                : null,
        );
    }
}