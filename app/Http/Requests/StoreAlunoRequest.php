<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlunoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Remove caracteres não numéricos antes da validação.
     */
    protected function prepareForValidation()
    {
        if ($this->has('cpf')) {
            $this->merge([
                'cpf' => preg_replace('/\D/', '', $this->cpf), // apenas números
            ]);
        }

        if ($this->has('rg')) {
            $this->merge([
                'rg' => preg_replace('/\D/', '', $this->rg),
            ]);
        }

        if ($this->has('cep')) {
            $this->merge([
                'cep' => preg_replace('/\D/', '', $this->cep),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            // Dados básicos do aluno
            'nomeCompleto' => 'required|string|max:255',
            'nomeSocial' => 'nullable|string|max:255',
            'dataNascimento' => 'required|date|before:today',
            'idade' => 'required|integer|min:0|max:120',
            'cpf' => ['required', 'string', 'unique:alunos,cpf', 'digits:11'],
            'rg' => 'nullable|string|max:20',
            
            // Mão Dominante
            'mao_dominante' => 'required|in:destro,canhoto',

            // Dados de Endereço
            'cep' => 'nullable|string|max:8',
            'rua' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:10',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'uf' => 'nullable|in:AC,AL,AP,AM,BA,CE,DF,ES,GO,MA,MT,MS,MG,PA,PB,PR,PE,PI,RJ,RN,RS,RO,RR,SC,SP,SE,TO',
            'telefone' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',

            // Dados de Trabalho (Sintaxe limpa de caracteres de espaço especiais)
            'carteiraTrabalho' => 'nullable|in:sim,nao',
            'jaTrabalhou' => 'nullable|in:sim,nao',
            'ctpsAssinada' => 'nullable|in:sim,nao',
            'qualFuncao' => 'nullable|string|max:255',

            // Escolaridade - REGRAS ATUALIZADAS
            'escola' => 'nullable|string|max:255',
            'ano' => 'nullable|string|max:20',
            'concluido' => 'required|boolean', // 0 = Não, 1 = Sim

            // Se CONCLUÍDO = NÃO (0), PERÍODO é obrigatório e deve ser 'manha', 'tarde' ou 'noite'
            'periodo' => 'nullable|required_if:concluido,0|in:manha,tarde,noite',
            
            // Se CONCLUÍDO = SIM (1), ANO DE CONCLUSÃO é obrigatório
            'anoConclusao' => 'nullable|required_if:concluido,1|string|max:10',
            
            'cursoAtual' => 'nullable|string|max:255',

            // Condição social e Benefícios
            'moradia' => 'nullable|string|max:50',
            'moradia_porquem' => 'nullable|string|max:255',
            'beneficio' => 'required|in:Sim,Não',
            'bolsa_familia' => 'nullable|numeric|min:0',
            'bpc_loas' => 'nullable|numeric|min:0',
            'pensao' => 'nullable|numeric|min:0',
            'aux_aluguel' => 'nullable|numeric|min:0',
            'renda_cidada' => 'nullable|numeric|min:0',
            'outros' => 'nullable|numeric|min:0',
            'observacoes' => 'nullable|string',
            
            // Gastos Essenciais (Adicionado para robustez do formulário)
            'agua' => 'nullable|numeric|min:0',
            'alimentacao' => 'nullable|numeric|min:0',
            'gas' => 'nullable|numeric|min:0',
            'luz' => 'nullable|numeric|min:0',
            'medicamento' => 'nullable|numeric|min:0',
            'telefone_internet' => 'nullable|numeric|min:0',
            'aluguel_financiamento' => 'nullable|numeric|min:0',


            // Saúde
            'ubs' => 'nullable|string|max:255',
            'convenio' => 'required|in:sim,nao',
            'qual_convenio' => 'nullable|string|max:255',
            'vacinacao' => 'required|in:sim,nao',
            'queixa_saude' => 'required|in:sim,nao',
            'qual_queixa' => 'nullable|string|max:255',
            'alergia' => 'required|in:sim,nao',
            'qual_alergia' => 'nullable|string|max:255',
            'tratamento' => 'required|in:sim,nao',
            'qual_tratamento' => 'nullable|string|max:255',
            'uso_remedio' => 'required|in:sim,nao',
            'qual_remedio' => 'nullable|string|max:255',
            'cirurgia' => 'required|in:sim,nao',
            'motivo_cirurgia' => 'nullable|string|max:255',
            'pcd' => 'required|in:sim,nao',
            'qual_pcd' => 'nullable|string|max:255',
            'necessidade_especial' => 'nullable|string|max:255',
            'doenca_congenita' => 'required|in:sim,nao',
            'qual_doenca_congenita' => 'nullable|string|max:255',
            'psicologo' => 'required|in:sim,nao',
            'quando_psicologo' => 'nullable|string|max:255',
            'convulsao' => 'required|in:sim,nao',
            'quando_convulsao' => 'nullable|string|max:255',
            'familia_doenca' => 'required|in:sim,nao',
            'qual_familia_doenca' => 'nullable|string|max:255',
            'familia_depressao' => 'required|in:sim,nao',
            'quem_familia_depressao' => 'nullable|string|max:255',
            'medico_especialista' => 'required|in:sim,nao',
            'qual_medico_especialista' => 'nullable|string|max:255',
            'familia_psicologico' => 'required|in:sim,nao',
            'quem_familia_psicologico' => 'nullable|string|max:255',
            'familia_alcool' => 'required|in:sim,nao',
            'quem_familia_alcool' => 'nullable|string|max:255',
            'familia_drogas' => 'required|in:sim,nao',
            'quem_familia_drogas' => 'nullable|string|max:255',

            // Outros
            'declaracao_consentimento' => 'required|accepted',
            'assinatura' => 'nullable|string',
            'familiares_json' => 'nullable|json',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'email' => 'O :attribute deve ser um endereço de e-mail válido.',
            'unique' => 'Já existe um aluno com esse :attribute.',
            'date' => 'O campo :attribute não é uma data válida.',
            'integer' => 'O campo :attribute deve ser um número inteiro.',
            'string' => 'O campo :attribute deve ser uma string.',
            'min' => 'O :attribute deve ter no mínimo :min caracteres ou valor.',
            'max' => 'O :attribute não pode ter mais de :max caracteres.',
            'size' => 'O :attribute deve ter exatamente :size caracteres.',
            'digits' => 'O campo :attribute deve conter exatamente :digits dígitos.',
            'in' => 'O valor selecionado para :attribute é inválido.',
            'accepted' => 'A declaração de consentimento deve ser aceita.',
            'json' => 'O formato de :attribute é inválido.',

            // Específicos
            'cpf.digits' => 'O CPF deve conter exatamente 11 números.',
            'dataNascimento.before' => 'A data de nascimento deve ser anterior a hoje.',
            'idade.max' => 'A idade não pode ser maior que 120 anos.',
            'mao_dominante.required' => 'O campo Mão Dominante é obrigatório.',
            
            // ESCOLARIDADE - MENSAGENS CONDICIONAIS
            'concluido.required' => 'A indicação de conclusão da escolaridade é obrigatória.',
            'periodo.required_if' => 'O campo Período é obrigatório quando a escolaridade não está concluída.',
            'periodo.in' => 'O valor selecionado para Período é inválido (escolha Manhã, Tarde ou Noite).',
            'anoConclusao.required_if' => 'O campo Ano de Conclusão é obrigatório quando a escolaridade está concluída.',

            // Mensagens específicas para campos de rádio
            'beneficio.required' => 'O campo Benefício é obrigatório.',
            'convenio.required' => 'O campo Convênio é obrigatório.',
            'vacinacao.required' => 'O campo Vacinação é obrigatório.',
            'queixa_saude.required' => 'O campo Queixa de Saúde é obrigatório.',
            'alergia.required' => 'O campo Alergia é obrigatório.',
            'tratamento.required' => 'O campo Tratamento é obrigatório.',
            'uso_remedio.required' => 'O campo Uso de Remédio é obrigatório.',
            'cirurgia.required' => 'O campo Cirurgia é obrigatório.',
            'pcd.required' => 'O campo PCD é obrigatório.',
            'doenca_congenita.required' => 'O campo Doença Congênita é obrigatório.',
            'psicologo.required' => 'O campo Psicólogo é obrigatório.',
            'convulsao.required' => 'O campo Convulsão é obrigatório.',
            'familia_doenca.required' => 'O campo Doença na Família é obrigatório.',
            'familia_depressao.required' => 'O campo Depressão na Família é obrigatório.',
            'medico_especialista.required' => 'O campo Médico Especialista é obrigatório.',
            'familia_psicologico.required' => 'O campo Problema Psicológico na Família é obrigatório.',
            'familia_alcool.required' => 'O campo Alcoolismo na Família é obrigatório.',
            'familia_drogas.required' => 'O campo Drogas na Família é obrigatório.',
        ];
    }
}