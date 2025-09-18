<x-guest-layout>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <div x-data="wizard()" class="w-full max-w-full mx-auto p-8 bg-white rounded-xl shadow-lg space-y-6">
        <form id="triagemForm" action="{{ route('aluno.store') }}" method="POST">
            @csrf
            <!-- Step 1: DADOS PESSOAIS DO(A) JOVEM -->
            <div x-show="step === 1" x-cloak x-transition>
                <h2 class="text-xl font-bold mb-4 text-brand border-b-2 border-brand pb-1">
                    1 - DADOS PESSOAIS DO(A) JOVEM
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <!-- Nome completo -->

                    <div>
                        <label for="nomeCompleto" class="block font-medium">Nome completo:</label>
                        <input type="text" id="nomeCompleto" name="nomeCompleto" required
                            class="mt-1 block w-full border rounded p-2">
                    </div>

                    <!-- Nome social -->
                    <div>
                        <label for="nomeSocial" class="block font-medium">Nome social:</label>
                        <input type="text" id="nomeSocial" name="nomeSocial"
                            class="mt-1 block w-full border rounded p-2">
                    </div>

                    <!-- Data de nascimento -->
                    <div>
                        <label for="dataNascimento" class="block font-medium">Data de nascimento:</label>
                        <input type="date" id="dataNascimento" name="dataNascimento" required
                            class="mt-1 block w-full border rounded p-2">
                    </div>

                    <!-- Idade -->
                    <div>
                        <label for="idade" class="block font-medium">Idade:</label>
                        <input type="number" id="idade" name="idade" min="0" class="mt-1 block w-full border rounded p-2">
                    </div>

                    <!-- CPF -->
                    <div>
                        <label for="cpf" class="block font-medium">CPF:</label>
                        <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00"
                            class="mt-1 block w-full border rounded p-2">
                    </div>

                    <!-- RG -->
                    <div>
                        <label for="rg" class="block font-medium">RG:</label>
                        <input type="text" id="rg" name="rg" class="mt-1 block w-full border rounded p-2">
                    </div>

                    <!-- Tem Carteira de Trabalho? -->
                    <div>
                        <label class="block font-medium">Tem Carteira de Trabalho?</label>
                        <div class="flex gap-4 mt-1">
                            <label><input type="radio" name="carteiraTrabalho" value="sim"> Sim</label>
                            <label><input type="radio" name="carteiraTrabalho" value="nao"> Não</label>
                        </div>
                    </div>

                    <!-- Já trabalhou? -->
                    <div>
                        <label class="block font-medium">Já trabalhou?</label>
                        <div class="flex gap-4 mt-1">
                            <label><input type="radio" name="jaTrabalhou" value="sim"> Sim</label>
                            <label><input type="radio" name="jaTrabalhou" value="nao"> Não</label>
                        </div>
                    </div>

                    <!-- Carteira de Trabalho assinada? -->
                    <div>
                        <label class="block font-medium">Carteira de Trabalho assinada?</label>
                        <div class="flex gap-4 mt-1">
                            <label><input type="radio" name="ctpsAssinada" value="sim"> Sim</label>
                            <label><input type="radio" name="ctpsAssinada" value="nao"> Não</label>
                        </div>
                    </div>

                    <!-- Qual função? -->
                    <div class="md:col-span-2">
                        <label for="qualFuncao" class="block font-medium">Qual função?</label>
                        <input type="text" id="qualFuncao" name="qualFuncao" class="mt-1 block w-full border rounded p-2">
                    </div>

                </div>
            </div>

            <!-- STEP 2: ENDEREÇO -->
            <div x-show="step === 2" x-cloak x-transition>
                <h2 class="text-xl font-bold mb-4 text-brand border-b-2 border-brand pb-1">2 - ENDEREÇO</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="text" name="cep" placeholder="CEP" class="border rounded p-2">
                    <input type="text" name="rua" placeholder="Rua/Av." class="border rounded p-2">
                    <input type="text" name="numero" placeholder="N°" class="border rounded p-2">
                    <input type="text" name="complemento" placeholder="Compl." class="border rounded p-2">
                    <input type="text" name="bairro" placeholder="Bairro" class="border rounded p-2">
                    <input type="text" name="cidade" placeholder="Cidade" class="border rounded p-2">
                    <select name="uf" class="border rounded p-2">
                        <option value="">UF</option>
                        @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                        <option value="{{ $uf }}">{{ $uf }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="telefone" placeholder="Tel." class="border rounded p-2">
                    <input type="text" name="celular" placeholder="Cel." class="border rounded p-2">
                    <input type="email" name="email" placeholder="E-mail" class="md:col-span-2 border rounded p-2">
                </div>
            </div>

            <!-- STEP 3: ESCOLARIDADE -->
            <div x-show="step === 3" x-cloak x-transition>
                <h2 class="text-xl font-bold mb-4 text-brand border-b-2 border-brand pb-1">3 - ESCOLARIDADE</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="text" name="escola" placeholder="Escola" class="border rounded p-2">
                    <select name="ano" class="border rounded p-2">
                        <option value="">Ano</option>
                        <option value="1º">1º</option>
                        <option value="2º">2º</option>
                        <option value="3º">3º</option>
                        <option value="9º">9º</option>
                    </select>
                    <input type="text" name="periodo" placeholder="Período" class="border rounded p-2">
                    <div>
                        <label>Concluído?</label>
                        <label><input type="radio" name="concluido" value="sim"> Sim</label>
                        <label><input type="radio" name="concluido" value="nao"> Não</label>
                    </div>
                    <input type="text" name="anoConclusao" placeholder="Ano de Conclusão" class="border rounded p-2">
                    <input type="text" name="cursoAtual" placeholder="Curso Atual" class="md:col-span-2 border rounded p-2">
                </div>
            </div>

            <!-- Step 4 - Dados Socioeconômicos -->
            <div x-show="step === 4" x-cloak x-transition>
                <h2 class="text-xl font-bold mb-4 text-brand border-b-2 border-brand pb-1">4- DADOS SOCIOECONÔMICOS</h2>

                <!-- Moradia -->
                <div class="mb-4">
                    <label class="font-semibold">Moradia:</label><br>
                    <label><input type="radio" name="moradia" value="Própria"> Própria</label>
                    <label><input type="radio" name="moradia" value="Cedida"> Cedida</label>
                    <label><input type="radio" name="moradia" value="Alugada"> Alugada</label>
                    <label><input type="radio" name="moradia" value="Financiada"> Financiada</label>
                    <input type="text" name="moradia_porquem" placeholder="Por quem?" class="border rounded p-1 mt-1 w-full">
                </div>

                <!-- Benefícios Sociais -->
                <div class="mb-4">
                    <label class="font-semibold">A família recebe algum benefício social?</label><br>
                    <label><input type="radio" name="beneficio" value="Sim"> Sim</label>
                    <label><input type="radio" name="beneficio" value="Não"> Não</label>

                    <div class="mt-2 grid grid-cols-2 gap-2">
                        <input type="text" name="bolsa_familia" placeholder="Bolsa Família R$" class="border rounded p-1">
                        <input type="text" name="bpc_loas" placeholder="BPC/LOAS R$" class="border rounded p-1">
                        <input type="text" name="pensao" placeholder="Pensão Alimentícia R$" class="border rounded p-1">
                        <input type="text" name="aux_aluguel" placeholder="Aux. Aluguel R$" class="border rounded p-1">
                        <input type="text" name="renda_cidada" placeholder="Renda Cidadã R$" class="border rounded p-1">
                        <input type="text" name="outros" placeholder="Outros R$" class="border rounded p-1">
                    </div>
                </div>

                <!-- Despesas Mensais / Observações -->
                <div class="mb-4">
                    <label class="font-semibold">Despesas Mensais / Observações:</label>
                    <textarea name="observacoes" placeholder="Observações..." class="border rounded p-1 w-full mt-1"></textarea>

                    <div class="grid grid-cols-2 gap-2 mt-2">
                        <input type="text" name="agua" placeholder="Água R$" class="border rounded p-1">
                        <input type="text" name="alimentacao" placeholder="Alimentação R$" class="border rounded p-1">
                        <input type="text" name="gas" placeholder="Gás R$" class="border rounded p-1">
                        <input type="text" name="luz" placeholder="Luz R$" class="border rounded p-1">
                        <input type="text" name="medicamento" placeholder="Medicamento R$" class="border rounded p-1">
                        <input type="text" name="telefone_internet" placeholder="Telefone/Internet R$" class="border rounded p-1">
                        <input type="text" name="aluguel_financiamento" placeholder="Aluguel/Financiamento R$" class="border rounded p-1">
                    </div>
                </div>

                <!-- Tabela de Familiares -->
                <div x-data="familyTable()" class="mt-6">
                    <label class="font-semibold mb-2 block">Parentesco e Renda Familiar:</label>

                    <table class="w-full border border-gray-300 text-left">
                        <thead class="bg-brand text-white rounded hover:bg-brand-dark">
                            <tr>
                                <th class="border px-2 py-1">Parentesco</th>
                                <th class="border px-2 py-1">Nome completo</th>
                                <th class="border px-2 py-1">Idade</th>
                                <th class="border px-2 py-1">Profissão</th>
                                <th class="border px-2 py-1">Empresa</th>
                                <th class="border px-2 py-1">Salário Base</th>
                                <th class="border px-2 py-1">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(row, index) in rows" :key="index">
                                <tr>
                                    <td><input type="text" x-model="row.parentesco" @input="updateInput()" class="w-full border rounded p-1"></td>
                                    <td><input type="text" x-model="row.nomeCompleto" @input="updateInput()" class="w-full border rounded p-1"></td>
                                    <td><input type="number" x-model="row.idade" @input="updateInput()" class="w-full border rounded p-1"></td>
                                    <td><input type="text" x-model="row.profissao" @input="updateInput()" class="w-full border rounded p-1"></td>
                                    <td><input type="text" x-model="row.empresa" @input="updateInput()" class="w-full border rounded p-1"></td>
                                    <td><input type="number" x-model="row.salarioBase" @input="updateInput()" class="w-full border rounded p-1"></td>
                                    <td class="text-center">
                                        <button type="button" @click="removeRow(index)" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-700">X</button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>

                    <button type="button" @click="addRow()" class="mt-2 px-4 py-2 bg-brand text-white rounded hover:bg-brand-dark">+ Adicionar</button>

                    <!-- Input escondido -->
                    <input type="hidden" name="familiares_json" id="familiaresInput">
                </div>

            </div>
            <!-- Step 5 - SAÚDE -->
            <div x-show="step === 5" x-cloak x-transition>
                <h2 class="text-xl font-bold mb-4 text-brand border-b-2 border-brand pb-1">5 - SAÚDE</h2>
                <p class="mb-4">Os dados abaixo nos ajudarão em caso de atendimento urgente e/ou emergente relacionados à saúde.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <!-- UBS -->
                    <div>
                        <label class="block font-medium">Qual UBS (posto de saúde) o jovem está matriculado?</label>
                        <input type="text" name="ubs" class="mt-1 block w-full border rounded p-2">
                    </div>

                    <!-- Convênio médico -->
                    <div>
                        <label class="block font-medium">Possui convênio médico?</label>
                        <div class="flex gap-4 mt-1">
                            <label><input type="radio" name="convenio" value="sim"> Sim</label>
                            <label><input type="radio" name="convenio" value="nao"> Não</label>
                        </div>
                        <input type="text" name="qual_convenio" placeholder="Se sim, qual?" class="mt-1 block w-full border rounded p-2">
                    </div>

                    <!-- Vacinação -->
                    <div>
                        <label class="block font-medium">A vacinação está em dia?</label>
                        <div class="flex gap-4 mt-1">
                            <label><input type="radio" name="vacinacao" value="sim"> Sim</label>
                            <label><input type="radio" name="vacinacao" value="nao"> Não</label>
                        </div>
                    </div>

                    <!-- Queixa de saúde -->
                    <div>
                        <label class="block font-medium">Apresenta alguma queixa de saúde no momento?</label>
                        <div class="flex gap-4 mt-1">
                            <label><input type="radio" name="queixa_saude" value="sim"> Sim</label>
                            <label><input type="radio" name="queixa_saude" value="nao"> Não</label>
                        </div>
                        <input type="text" name="qual_queixa" placeholder="Se sim, qual?" class="mt-1 block w-full border rounded p-2">
                    </div>

                    <!-- Alergia -->
                    <div>
                        <label class="block font-medium">Possui alguma alergia?</label>
                        <div class="flex gap-4 mt-1">
                            <label><input type="radio" name="alergia" value="sim"> Sim</label>
                            <label><input type="radio" name="alergia" value="nao"> Não</label>
                        </div>
                        <input type="text" name="qual_alergia" placeholder="Se sim, qual?" class="mt-1 block w-full border rounded p-2">
                    </div>

                    <!-- Tratamento -->
                    <div>
                        <label class="block font-medium">Já fez ou faz algum tipo de tratamento?</label>
                        <div class="flex gap-4 mt-1">
                            <label><input type="radio" name="tratamento" value="sim"> Sim</label>
                            <label><input type="radio" name="tratamento" value="nao"> Não</label>
                        </div>
                        <input type="text" name="qual_tratamento" placeholder="Se sim, qual?" class="mt-1 block w-full border rounded p-2">
                    </div>

                    <!-- Uso regular de remédio -->
                    <div>
                        <label class="block font-medium">Faz uso regular de algum remédio?</label>
                        <div class="flex gap-4 mt-1">
                            <label><input type="radio" name="uso_remedio" value="sim"> Sim</label>
                            <label><input type="radio" name="uso_remedio" value="nao"> Não</label>
                        </div>
                        <input type="text" name="qual_remedio" placeholder="Se sim, qual?" class="mt-1 block w-full border rounded p-2">
                    </div>

                    <!-- Cirurgia -->
                    <div>
                        <label class="block font-medium">Já fez alguma cirurgia?</label>
                        <div class="flex gap-4 mt-1">
                            <label><input type="radio" name="cirurgia" value="sim"> Sim</label>
                            <label><input type="radio" name="cirurgia" value="nao"> Não</label>
                        </div>
                        <input type="text" name="motivo_cirurgia" placeholder="Se sim, qual o motivo?" class="mt-1 block w-full border rounded p-2">
                    </div>

                    <!-- PCD / Necessidade especial -->
                    <div>
                        <label class="block font-medium">É PCD (Pessoa Com Deficiência)?</label>
                        <div class="flex gap-4 mt-1">
                            <label><input type="radio" name="pcd" value="sim"> Sim</label>
                            <label><input type="radio" name="pcd" value="nao"> Não</label>
                        </div>
                        <input type="text" name="qual_pcd" placeholder="Se sim, qual?" class="mt-1 block w-full border rounded p-2">
                    </div>

                    <div>
                        <label class="block font-medium">Em função disso, possui alguma necessidade especial?</label>
                        <input type="text" name="necessidade_especial" class="mt-1 block w-full border rounded p-2">
                    </div>

                    <!-- Doença congênita/hereditária -->
                    <div>
                        <label class="block font-medium">Tem alguma doença congênita e/ou hereditária?</label>
                        <div class="flex gap-4 mt-1">
                            <label><input type="radio" name="doenca_congenita" value="sim"> Sim</label>
                            <label><input type="radio" name="doenca_congenita" value="nao"> Não</label>
                        </div>
                        <input type="text" name="qual_doenca_congenita" placeholder="Se sim, qual?" class="mt-1 block w-full border rounded p-2">
                    </div>

                    <!-- Psicólogo/Psiquiatra -->
                    <div>
                        <label class="block font-medium">Está passando com psicólogo e/ou psiquiatra ou já passou?</label>
                        <div class="flex gap-4 mt-1">
                            <label><input type="radio" name="psicologo" value="sim"> Sim</label>
                            <label><input type="radio" name="psicologo" value="nao"> Não</label>
                        </div>
                        <input type="text" name="quando_psicologo" placeholder="Se sim, quando?" class="mt-1 block w-full border rounded p-2">
                    </div>

                    <!-- Convulsões / epilepsia / desmaios -->
                    <div>
                        <label class="block font-medium">Tem ou já teve convulsões, epilepsia ou desmaios?</label>
                        <div class="flex gap-4 mt-1">
                            <label><input type="radio" name="convulsao" value="sim"> Sim</label>
                            <label><input type="radio" name="convulsao" value="nao"> Não</label>
                        </div>
                        <input type="text" name="quando_convulsao" placeholder="Se sim, quando?" class="mt-1 block w-full border rounded p-2">
                    </div>

                    <!-- Histórico familiar de doenças -->
                    <div class="md:col-span-2">
                        <label class="block font-medium">Algum membro da família possui alguma doença congênita e/ou hereditária? (Ex. Hipertensão, hipotireoidismo, diabetes, outros)</label>
                        <div class="flex gap-4 mt-1">
                            <label><input type="radio" name="familia_doenca" value="sim"> Sim</label>
                            <label><input type="radio" name="familia_doenca" value="nao"> Não</label>
                        </div>
                        <input type="text" name="qual_familia_doenca" placeholder="Se sim, quem?" class="mt-1 block w-full border rounded p-2">
                    </div>

                    <!-- Medicamentos, acompanhamento e abuso de álcool/drogas -->
                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">

                        <div>
                            <label class="block font-medium">Algum membro da família faz uso de medicamentos para sintomas depressivos ou ansiosos?</label>
                            <div class="flex gap-4 mt-1">
                                <label><input type="radio" name="familia_depressao" value="sim"> Sim</label>
                                <label><input type="radio" name="familia_depressao" value="nao"> Não</label>
                            </div>
                            <input type="text" name="quem_familia_depressao" placeholder="Se sim, quem?" class="mt-1 block w-full border rounded p-2">
                        </div>

                        <div>
                            <label class="block font-medium">Está passando com algum médico especialista?</label>
                            <div class="flex gap-4 mt-1">
                                <label><input type="radio" name="medico_especialista" value="sim"> Sim</label>
                                <label><input type="radio" name="medico_especialista" value="nao"> Não</label>
                            </div>
                            <input type="text" name="qual_medico_especialista" placeholder="Se sim, qual?" class="mt-1 block w-full border rounded p-2">
                        </div>

                        <div>
                            <label class="block font-medium">Algum membro da família faz acompanhamento psicológico?</label>
                            <div class="flex gap-4 mt-1">
                                <label><input type="radio" name="familia_psicologico" value="sim"> Sim</label>
                                <label><input type="radio" name="familia_psicologico" value="nao"> Não</label>
                            </div>
                            <input type="text" name="quem_familia_psicologico" placeholder="Se sim, quem?" class="mt-1 block w-full border rounded p-2">
                        </div>

                        <div>
                            <label class="block font-medium">Algum membro da família faz uso abusivo de bebida alcoólica?</label>
                            <div class="flex gap-4 mt-1">
                                <label><input type="radio" name="familia_alcool" value="sim"> Sim</label>
                                <label><input type="radio" name="familia_alcool" value="nao"> Não</label>
                            </div>
                            <input type="text" name="quem_familia_alcool" placeholder="Se sim, quem?" class="mt-1 block w-full border rounded p-2">
                        </div>

                        <div>
                            <label class="block font-medium">Algum membro da família faz uso abusivo de drogas?</label>
                            <div class="flex gap-4 mt-1">
                                <label><input type="radio" name="familia_drogas" value="sim"> Sim</label>
                                <label><input type="radio" name="familia_drogas" value="nao"> Não</label>
                            </div>
                            <input type="text" name="quem_familia_drogas" placeholder="Se sim, quem?" class="mt-1 block w-full border rounded p-2">
                        </div>

                    </div>

                </div>
            </div>
            <!-- Step 6 - Declaração e Consentimento -->
            <div x-show="step === 6" x-cloak x-transition class="mb-4">
                <h2 class="text-xl font-bold mb-4 text-brand border-b-2 border-brand pb-1">Declaração e Consentimento</h2>

                <div class="border rounded p-4 bg-gray-50 text-sm text-gray-700">
                    <p class="mb-2">
                        Declaro que as informações aqui prestadas são verdadeiras e que assumo a responsabilidade pelas mesmas, sabendo que posso ser excluído(a) da triagem se comprovada a falsidade das minhas declarações.
                    </p>
                    <p class="mb-2">
                        A SODIPROM fica desde já autorizada a compartilhar os dados pessoais coletados na ficha de inscrição e ficha de saúde, com sua área interna de gestão de pessoas, com o gestor da área que deu origem à vaga, com empresas de recrutamento e seleção, com redes sociais de negócios e com empresas terceiras que fornecem licença de software para armazenamento e gestão de dados.
                    </p>
                    <p class="mb-2">
                        A SODIPROM responsabiliza-se pela manutenção de medidas de segurança, técnicas e administrativas aptas a proteger os dados pessoais de acessos não autorizados e de situações acidentais ou ilícitas de destruição, perda, alteração, comunicação ou qualquer forma de tratamento inadequado ou ilícito.
                    </p>
                    <p class="mb-2">
                        Em conformidade ao artigo 48 da Lei nº 13.709, a SODIPROM comunicará ao titular e à Autoridade Nacional de Proteção de Dados – ANPD a ocorrência de incidente de segurança que possa acarretar risco ou dano relevante ao titular do dado.
                    </p>
                </div>

                <div class="mt-2">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="declaracao_consentimento" required class="form-checkbox h-5 w-5 text-blue-600">
                        Li e concordo com a declaração acima
                    </label>
                </div>
            </div>

            <!-- STEP 7: ASSINATURA E FINALIZAÇÃO -->
            <div x-show="step === 7" x-cloak x-transition class="space-y-6">

                <!-- Assinatura -->
                <div class="flex flex-col items-center">
                    <label class="block font-medium mb-1">Assinatura do responsável:</label>
                    <canvas id="assinaturaCanvas" width="600" height="200" class="border rounded" style="max-width:100%; height:auto;"></canvas>
                    <button type="button" @click="limparAssinatura()" class="mt-2 px-4 py-2 bg-red-500 text-white rounded">Limpar</button>
                    <input type="hidden" name="assinatura" id="assinaturaInput">
                </div>
            </div>
            <!-- Navegação -->
            <div class="flex justify-between mt-6">
                <button type="button" @click="prevStep()" x-show="step > 1" class="px-4 py-2 bg-gray-500 text-white rounded">
                    Anterior
                </button>
                <button type="button" @click="nextStep()" x-show="step < 7" class="px-4 py-2 bg-brand text-white rounded hover:bg-brand-dark">
                    Próximo
                </button>
                <button type="submit" x-show="step === 7" @click="salvarAssinatura()" class="px-4 py-2 bg-brand text-white rounded hover:bg-brand-dark">
                    Cadastrar
                </button>
            </div>
        </form>
    </div>

    <!-- SCRIPT ALPINE -->
    <script>
        window.wizard = function() {
            return {
                step: 1,
                nextStep() {
                    if (this.step < 7) this.step++
                },
                prevStep() {
                    if (this.step > 1) this.step--
                },
                salvarAssinatura() {
                    const canvas = document.getElementById('assinaturaCanvas');
                    if (canvas) document.getElementById('assinaturaInput').value = canvas.toDataURL();
                    this.atualizarFamiliaresInput();
                },
                limparAssinatura() {
                    const canvas = document.getElementById('assinaturaCanvas');
                    if (!canvas) return;
                    const ctx = canvas.getContext('2d');
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                },
                atualizarFamiliaresInput() {
                    const familyDiv = document.querySelector('[x-data="familyTable()"]');
                    if (familyDiv && familyDiv.__x) document.getElementById('familiaresInput').value = JSON.stringify(familyDiv.__x.$data.rows);
                }
            }
        }

        function familyTable() {
        return {
            rows: [],
            addRow() {
                this.rows.push({
                    parentesco: '',
                    nomeCompleto: '',
                    idade: null,
                    profissao: '',
                    empresa: '',
                    salarioBase: null
                });
                this.updateInput();
            },
            removeRow(index) {
                this.rows.splice(index, 1);
                this.updateInput();
            },
            updateInput() {
                // Atualiza o hidden input com o JSON mais recente
                const input = document.getElementById('familiaresInput');
                if (input) input.value = JSON.stringify(this.rows);
            }
        }
    }

        // Canvas de assinatura
        document.addEventListener('DOMContentLoaded', () => {
            const canvas = document.getElementById('assinaturaCanvas');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            let drawing = false;
            let lastX = 0;
            let lastY = 0;

            function getPos(e) {
                const rect = canvas.getBoundingClientRect();
                if (e.touches && e.touches.length > 0) {
                    return {
                        x: e.touches[0].clientX - rect.left,
                        y: e.touches[0].clientY - rect.top
                    };
                } else {
                    return {
                        x: e.clientX - rect.left,
                        y: e.clientY - rect.top
                    };
                }
            }

            function start(e) {
                drawing = true;
                const pos = getPos(e);
                lastX = pos.x;
                lastY = pos.y;
                e.preventDefault();
            }

            function end(e) {
                drawing = false;
                e.preventDefault();
            }

            function move(e) {
                if (!drawing) return;
                const pos = getPos(e);
                ctx.beginPath();
                ctx.moveTo(lastX, lastY);
                ctx.lineTo(pos.x, pos.y);
                ctx.stroke();
                lastX = pos.x;
                lastY = pos.y;
                e.preventDefault();
            }

            // Eventos mouse
            canvas.addEventListener('mousedown', start);
            canvas.addEventListener('mousemove', move);
            canvas.addEventListener('mouseup', end);
            canvas.addEventListener('mouseout', end);

            // Eventos touch
            canvas.addEventListener('touchstart', start);
            canvas.addEventListener('touchmove', move);
            canvas.addEventListener('touchend', end);
            canvas.addEventListener('touchcancel', end);

            // Configurações do canvas
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#000';

            // Garantir que familiaresInput seja sempre atualizado antes do submit
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', () => {
                    const familyDiv = document.querySelector('[x-data="familyTable()"]');
                    if (familyDiv && familyDiv.__x) {
                        // 👉 NOVA LINHA DE CÓDIGO AQUI
                        debugger;

                        document.getElementById('familiaresInput').value = JSON.stringify(familyDiv.__x.$data.rows);
                    }
                    if (canvas) {
                        document.getElementById('assinaturaInput').value = canvas.toDataURL();
                    }
                });
            }
        });
    </script>
</x-guest-layout>