<?php

namespace App\Http\Controllers;

use App\Models\Aluno;
use App\Models\Familiar;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FamiliarController extends Controller
{
    /**
     * Adiciona um novo familiar ao aluno.
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Aluno $aluno
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Aluno $aluno)
    {
        // 1. Validação dos dados do Familiar
        $validatedData = $request->validate([
            'nomeCompleto' => 'required|string|max:191',
            'parentesco' => 'required|string|max:50',
            'idade' => 'nullable|integer|min:0',
            'profissao' => 'nullable|string|max:191',
            'salarioBase' => 'nullable|numeric',
            // Adicione outras regras se o Model Familiar tiver mais campos (Ex: telefone, etc.)
        ]);

        // 2. Associa o familiar ao aluno
        $aluno->familiares()->create($validatedData);

        return redirect()->route('aluno.edit', $aluno)
                         ->with('success', 'Familiar adicionado com sucesso!');
    }

    /**
     * Remove um familiar.
     * @param \App\Models\Familiar $familiar
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Familiar $familiar)
    {
        // 1. Guarda o aluno_id antes de deletar para redirecionar
        $aluno = $familiar->aluno; 
        
        // 2. Deleta o familiar
        $familiar->delete();

        // 3. Redireciona de volta para a tela de edição do aluno
        return redirect()->route('aluno.edit', $aluno)
                         ->with('success', 'Familiar removido com sucesso!');
    }
}