<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Attendance; // Supondo um model de presença
use Carbon\Carbon;

class AttendanceTable extends Component
{
    // Propriedades reativas (Livewire detecta mudanças automaticamente)
    public $aluno;
    public $attendanceData = []; // Array para armazenar o status de presença (P, F, J, D, -)
    public $dates = []; // Array de datas visíveis na tabela

    public function mount($aluno)
    {
        $this->aluno = $aluno;
        $this->dates = $this->generateDatesForOctober(); // Gera as datas do mês
        $this->loadAttendanceData(); // Carrega o status de presença/falta
    }

    private function generateDatesForOctober()
    {
        // Simulação de datas
        $start = Carbon::create(2025, 10, 7);
        $end = Carbon::create(2025, 10, 20);
        $dates = [];
        for ($date = $start; $date->lte($end); $date->addDay()) {
             // Simulação de dias de descanso
            $status = in_array($date->day, [11, 12, 18, 19]) ? 'D' : '-';
            $dates[$date->format('Y-m-d')] = [
                'day' => $date->day,
                'weekday' => $date->format('D'),
                'status' => $status
            ];
        }
        return $dates;
    }
    
    // Simulação: Carregar dados do banco
    private function loadAttendanceData()
    {
        // Na vida real: buscaria no banco de dados
        // $attendanceRecords = Attendance::where('aluno_id', $this->aluno['id'])->get();
        
        // Dados iniciais simulados (P = 2, F = 3, J = 1)
        $this->attendanceData = [
            '2025-10-07' => 'F',
            '2025-10-08' => 'P',
            '2025-10-09' => 'P',
            '2025-10-13' => 'F',
            '2025-10-14' => 'J',
            '2025-10-15' => 'F',
            // ... e o resto seria '-' ou 'D'
        ];
    }
    
    // Função CHAVE: Atualiza o status da presença
    public function updateStatus($dateKey, $currentStatus)
    {
        // Ciclo de Status: -, P, F, J (ignorando 'D' que é fixo)
        $statusCycle = ['-', 'P', 'F', 'J'];
        
        // Pega o próximo status no ciclo. Se for 'D' (Descanso), não faz nada.
        if ($currentStatus === 'D') {
            return; 
        }

        $currentIndex = array_search($currentStatus, $statusCycle);
        $nextIndex = ($currentIndex !== false && $currentIndex < count($statusCycle) - 1) 
                     ? $currentIndex + 1 
                     : 0; // Volta para o primeiro ('-')

        $newStatus = $statusCycle[$nextIndex];
        
        // 1. ATUALIZA A PROPRIEDADE REATIVA (Livewire envia o AJAX)
        $this->attendanceData[$dateKey] = $newStatus;
        
        // 2. SALVAR NO BANCO DE DADOS (Aqui é onde o Laravel entra)
        // Attendance::updateOrCreate(
        //     ['aluno_id' => $this->aluno['id'], 'date' => $dateKey],
        //     ['status' => $newStatus]
        // );
        
        // O Livewire se encarrega de re-renderizar o HTML automaticamente.
    }

    public function render()
    {
        // 3. CÁLCULO DOS TOTAIS NO BACKEND (Sempre correto!)
        $totalPres = count(array_filter($this->attendanceData, fn($s) => $s === 'P'));
        // Faltas (F e J)
        $totalFaltas = count(array_filter($this->attendanceData, fn($s) => $s === 'F' || $s === 'J'));
        
        return view('livewire.attendance-table', [
            'totalPres' => $totalPres,
            'totalFaltas' => $totalFaltas,
        ]);
    }
}