<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB; 

use App\Models\Aluno;
use App\Models\User;

class Turma extends Model
{
    use HasFactory;

    protected $fillable = [
        'periodo',
        'letra',
        'ano_letivo',
        'vagas',
        'data_inicio', 
        'data_fim',    
        'professor_id',
    ];

    public function professor()
    {
        return $this->belongsTo(User::class, 'professor_id');
    }

    public function alunos(): HasMany
    {
        return $this->hasMany(Aluno::class, 'turma_id');
    }

    /**
     * Accessor que retorna o nome completo da turma com o período formatado.
     * Ex: "2024 - A (1º Semestre)"
     */
    public function getNomeCompletoAttribute()
    {
        // Mapeia os valores do DB ('1' e '2') para o formato de exibição.
        $periodoDisplay = match((string)$this->periodo) {
            '1' => '1º Semestre',
            '2' => '2º Semestre',
            default => $this->periodo,
        };
        
        return "{$this->ano_letivo} - {$this->letra} ({$periodoDisplay})";
    }


    /**
     * Determina o próximo índice alfabético para um dado ano letivo.
     * 🚨 CHAVE: A busca pela última letra é GLOBAL para o ano, garantindo continuidade entre os períodos.
     * @param int $anoLetivo
     * @return int O índice alfabético (0 para 'A', 1 para 'B', etc.)
     */
    public static function getNextAlphaIndex(int $anoLetivo): int
    {
        // Encontra a turma com a letra de maior ordem (Z) para o ano, em QUALQUER período.
        $lastTurma = self::where('ano_letivo', $anoLetivo)
                          ->whereNotNull('letra')
                          ->orderBy('letra', 'desc') 
                          ->first();

        if (!$lastTurma) {
            return 0; // Começa em 'A' (índice 0)
        }

        // Converte a última letra encontrada para o próximo índice (ex: 'A' -> 1)
        return ord($lastTurma->letra) - ord('A') + 1;
    }

    /**
     * Tenta encontrar a turma correta com base no código do aluno (que inclui ano/semestre).
     * @param string $codigoAluno O código no formato AAAA-S-T-X
     * @return int|null O ID da Turma ou null se não encontrada.
     */
    public static function findTurmaIdByCodigoAluno(string $codigoAluno): ?int
    {
        // Padrão esperado: AAAA (Ano) - S (Semestre) - T (Turma) - X (Sequencial)
        // Ex: 2024-1-A-001 (Ano 2024, 1º Semestre, Turma A)
        if (preg_match('/^(\d{4})-(\d)-([A-Z])-\d+$/i', $codigoAluno, $matches)) {
            $anoLetivo = (int) $matches[1];
            $periodo = $matches[2]; // Deve ser '1' ou '2' (Semestre)
            $letra = strtoupper($matches[3]); // Letra da Turma, ex: 'A'

            // Garante que o período é um semestre válido (1 ou 2)
            if (!in_array($periodo, ['1', '2'])) {
                return null;
            }

            $turma = self::where('ano_letivo', $anoLetivo)
                          ->where('periodo', $periodo)
                          ->where('letra', $letra)
                          ->first();
                          
            return $turma->id ?? null;
        }

        return null;
    }
}