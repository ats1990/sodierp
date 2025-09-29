<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Aluno extends Model
{
    use HasFactory;

    protected $table = 'alunos';

    protected $fillable = [
        'nomeCompleto','nomeSocial','dataNascimento','idade','cpf','rg',
        'carteiraTrabalho','jaTrabalhou','ctpsAssinada','qualFuncao',
        'cep','rua','numero','complemento','bairro','cidade','uf',
        'telefone','celular','email','escola','ano','periodo','concluido',
        'anoConclusao','cursoAtual','moradia','moradia_porquem','beneficio',
        'bolsa_familia','bpc_loas','pensao','aux_aluguel','renda_cidada',
        'outros','observacoes','agua','alimentacao','gas','luz','medicamento',
        'telefone_internet','aluguel_financiamento','ubs','convenio','qual_convenio',
        'vacinacao','queixa_saude','qual_queixa','alergia','qual_alergia',
        'tratamento','qual_tratamento','uso_remedio','qual_remedio','cirurgia',
        'motivo_cirurgia','pcd','qual_pcd','necessidade_especial','doenca_congenita',
        'qual_doenca_congenita','psicologo','quando_psicologo','convulsao','quando_convulsao',
        'familia_doenca','qual_familia_doenca','familia_depressao','quem_familia_depressao',
        'medico_especialista','qual_medico_especialista','familia_psicologico',
        'quem_familia_psicologico','familia_alcool','quem_familia_alcool','familia_drogas',
        'quem_familia_drogas','declaracao_consentimento','assinatura',
    ];

    // Campos booleanos do formulário
    protected array $booleanFields = [
        'carteiraTrabalho','jaTrabalhou','ctpsAssinada','concluido','beneficio',
        'convenio','vacinacao','queixa_saude','alergia','tratamento','uso_remedio',
        'cirurgia','pcd','doenca_congenita','psicologo','convulsao','familia_doenca',
        'familia_depressao','medico_especialista','familia_psicologico','familia_alcool',
        'familia_drogas','declaracao_consentimento'
    ];

    // Relacionamento com familiares
    public function familiares(): HasMany
    {
        return $this->hasMany(Familiar::class);
    }

    // Mutator genérico para booleanos
    protected static function booted()
    {
        static::saving(function ($aluno) {
            foreach ($aluno->booleanFields as $field) {
                if (isset($aluno->$field)) {
                    $aluno->$field = in_array($aluno->$field, ['sim','on',true,1], true);
                }
            }

            // Sanitiza CPF
            if (isset($aluno->cpf)) {
                $aluno->cpf = preg_replace('/[^0-9]/', '', $aluno->cpf);
            }
        });
    }
}
