<div>
    {{-- A TR deve ser encapsulada por causa do Livewire --}}
    <tr class="border-b border-gray-200 hover:bg-gray-50">
        <td class="py-3 px-4 text-left whitespace-nowrap font-medium w-64">{{ $aluno['name'] }}</td>

        {{-- Loop sobre as datas para criar as células --}}
        @foreach ($dates as $dateKey => $dateInfo)
            @php
                // Pega o status atual para esta data/aluno
                $status = $attendanceData[$dateKey] ?? $dateInfo['status']; 
                // Define a classe CSS baseada no status
                $statusClass = 'status-' . ($status == '-' ? 'N' : $status); 
            @endphp

            <td 
                class="status-cell {{ $statusClass }}" 
                style="width: 35px; height: 35px; text-align: center; cursor: pointer;"
                {{-- A CHAVE: Chama a função PHP quando a célula é clicada --}}
                wire:click="updateStatus('{{ $dateKey }}', '{{ $status }}')"
            >
                {{ $status }}
            </td>
        @endforeach

        {{-- Colunas de Totais (Renderizadas automaticamente pelo Livewire) --}}
        <td class="py-3 px-4 font-extrabold text-blue-600 text-center w-24">{{ $totalPres }}</td>
        <td class="py-3 px-4 font-extrabold text-red-600 text-center w-24">{{ $totalFaltas }}</td>
    </tr>
</div>