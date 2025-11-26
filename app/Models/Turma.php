<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'is_active', // <-- NOVO CAMPO
    ];

    // ... (Métodos professor() e alunos() permanecem iguais)

    /**
     * Accessor que retorna o nome completo da turma com o período formatado.
     * Ex: "2025 - A (1º Semestre)"
     */
    public function alunos(): HasMany
    {
        // Assume que a chave estrangeira na tabela 'alunos' é 'turma_id'
        return $this->hasMany(Aluno::class, 'turma_id');
    }
    public function getNomeCompletoAttribute()
    {
        // Garante que o ano seja exibido com 4 dígitos.
        $ano = strlen((string)$this->ano_letivo) === 2 
            ? 2000 + (int)$this->ano_letivo 
            : $this->ano_letivo;

        $periodoDisplay = match((string)$this->periodo) {
            '1' => '1º Semestre',
            '2' => '2º Semestre',
            default => $this->periodo,
        };
        
        return "{$ano} - {$this->letra} ({$periodoDisplay})" . ($this->is_active ? '' : ' (Histórica)');
    }

    /**
     * Tenta encontrar a turma, ou cria uma nova se não for encontrada (Turma Histórica).
     *
     * @param string $codigoAluno O código no formato Ex: 251TA1
     * @return int|null O ID da Turma encontrada ou criada.
     */
    public static function findOrCreateTurmaIdByCodigoAluno(string $codigoAluno): ?int
    {
        if (!preg_match('/^(\d{2})(\d)([A-Z]+)(\d+)$/i', $codigoAluno, $matches)) {
            return null; // O código não corresponde ao padrão
        }

        // 1. Extração dos Componentes
        $anoLetivoAbreviado = (string) $matches[1]; // '25'
        $periodo = $matches[2];                     // '1'
        $letraCompleta = strtoupper($matches[3]);    // 'TA'
        $letraBusca = substr($letraCompleta, -1);   // 'A'
        $anoLetivoCompleto = 2000 + (int)$anoLetivoAbreviado; // '2025'
        
        if (!in_array($periodo, ['1', '2'])) {
            return null; // Período inválido
        }
        
        // 2. Tenta buscar com 4 ou 2 dígitos (critérios para a busca)
        $turma = self::where('periodo', $periodo)
                     ->where('letra', $letraBusca)
                     ->where(function ($query) use ($anoLetivoAbreviado, $anoLetivoCompleto) {
                         $query->where('ano_letivo', $anoLetivoAbreviado)
                               ->orWhere('ano_letivo', $anoLetivoCompleto);
                     })
                     ->first();


        // Se a turma foi encontrada (ativa ou já migrada), retorna o ID
        if ($turma) {
            return $turma->id;
        }

        // 3. Criação Automática da Turma Histórica
        
        try {
            $novaTurma = self::create([
                // Usamos o ano completo na criação para evitar ambiguidade (2025)
                'ano_letivo' => $anoLetivoCompleto, 
                'periodo' => $periodo,
                'letra' => $letraBusca,
                'vagas' => 0, 
                'is_active' => false, // <-- MARCA COMO HISTÓRICA / INATIVA
                'data_inicio' => null, 
                'data_fim' => null,   
                'professor_id' => null, 
            ]);

            return $novaTurma->id;

        } catch (\Exception $e) {
            // Falha na criação (ex: campos NOT NULL faltando)
            return null; 
        }
    }
    
    // O método getNextAlphaIndex não é mais relevante neste contexto de importação histórica
    // e pode ser removido, se desejar, mas vou mantê-lo aqui:
    public static function getNextAlphaIndex(int $anoLetivo): int
    {
        $lastTurma = self::where('ano_letivo', $anoLetivo)
                         ->whereNotNull('letra')
                         ->orderBy('letra', 'desc') 
                         ->first();

        if (!$lastTurma) {
            return 0; 
        }
        return ord($lastTurma->letra) - ord('A') + 1;
    }
}