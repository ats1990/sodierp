<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log; // 👈 Necessário para usar Log::error
use Illuminate\Support\Facades\Storage; // 👈 Necessário para apagar o arquivo

use App\Imports\AlunoImport;

class ProcessAlunoImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $filePath;
    protected string $delimiter;

    /**
     * O número de segundos que o job pode rodar antes de atingir o timeout.
     */
    public int $timeout = 1200; // 20 minutos

    /**
     * Cria uma nova instância do job.
     *
     * @param string $filePath Caminho completo para o arquivo temporário
     * @param string $delimiter Delimitador detectado (ex: ',', ';')
     * @return void
     */
    public function __construct(string $filePath, string $delimiter)
    {
        // Armazena o caminho do arquivo (temporário) e o delimitador
        $this->filePath = $filePath;
        $this->delimiter = $delimiter;
    }

    /**
     * Executa o job (roda em background).
     *
     * @return void
     */
    public function handle()
    {
        try {
            // 1. Executa a importação usando o Maatwebsite/Excel
            // A classe AlunoImport fará o mapeamento e a persistência no DB.
            Excel::import(new AlunoImport($this->delimiter), $this->filePath);

            // Opcional: registrar sucesso
            Log::info("Importação de Alunos do arquivo {$this->filePath} concluída com sucesso.");
            
        } catch (\Exception $e) {
            
            // 2. Registra o erro detalhadamente, sem o \ global
            Log::error("Erro na importação em background: " . $e->getMessage(), [ 
                'file' => $this->filePath,
                'delimiter' => $this->delimiter,
                'trace' => $e->getTraceAsString(), // Adiciona o stack trace para depuração
            ]);
            
            // Re-throw para o Laravel re-tentar o Job, se configurado
            throw $e; 
            
        } finally {
            // 3. Garante que o arquivo temporário seja apagado no final (sucesso ou falha)
            if (Storage::exists($this->filePath)) {
                Storage::delete($this->filePath);
            }
        }
    }
}