<x-guest-layout>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/imask"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div x-data="wizard()" class="w-full max-w-full mx-auto p-8 bg-white rounded-xl shadow-lg space-y-6">
        <form id="triagemForm" action="{{ route('aluno.store') }}" method="POST">
            @csrf

            <!-- Step 1: DADOS PESSOAIS DO(A) JOVEM -->
            <div x-show="step === 1" x-cloak x-transition
                x-data="{
        trabalhou: '{{ old('jaTrabalhou', '') }}',
        temCT: '{{ old('carteiraTrabalho', '') }}',
        ctAssinada: '{{ old('ctpsAssinada', '') }}'
     }">

                @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <strong>Ops!</strong> Corrija os erros abaixo:
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <h2 class="text-xl font-bold mb-4 text-brand border-b-2 border-brand pb-1">
                    Dados Pessoais do(a) Jovem
                </h2>

                <div class="space-y-4">
                    <!-- Linha 1: Nome completo | Nome Social -->
                    <div class="flex space-x-4">
                        <div class="flex-1">
                            <label for="nomeCompleto" class="block font-medium">Nome completo</label>
                            <input type="text" id="nomeCompleto" name="nomeCompleto" value="{{ old('nomeCompleto') }}"
                                class="w-full border rounded px-3 py-2 text-sm">
                        </div>
                        <div class="flex-1">
                            <label for="nomeSocial" class="block font-medium">Nome Social</label>
                            <input type="text" id="nomeSocial" name="nomeSocial" value="{{ old('nomeSocial') }}"
                                class="w-full border rounded px-3 py-2 text-sm">
                        </div>
                    </div>

                    <!-- Linha 2: Data de Nascimento | Idade -->
                    <div class="flex space-x-4">
                        <div class="flex-1">
                            <label for="dataNascimento" class="block font-medium">Data de Nascimento</label>
                            <input type="date" id="dataNascimento" name="dataNascimento" value="{{ old('dataNascimento') }}"
                                class="w-full border rounded px-3 py-2 text-sm">
                        </div>
                        <div class="flex-1">
                            <label for="idade" class="block font-medium">Idade</label>
                            <input type="number" id="idade" name="idade" value="{{ old('idade') }}"
                                class="w-full border rounded px-3 py-2 text-sm">
                        </div>
                    </div>

                    <!-- Linha 3: CPF | RG -->
                    <div class="flex space-x-4">
                        <div class="flex-1">
                            <label for="cpf" class="block font-medium">CPF</label>
                            <input type="text" id="cpf" name="cpf" value="{{ old('cpf') }}"
                                class="w-full border rounded px-3 py-2 text-sm">
                        </div>
                        <div class="flex-1">
                            <label for="rg" class="block font-medium">RG</label>
                            <input type="text" id="rg" name="rg" value="{{ old('rg') }}"
                                class="w-full border rounded px-3 py-2 text-sm">
                        </div>
                    </div>

                    <!-- Linha 4: Já trabalhou | Tem Carteira | CT assinada -->
                    <div class="flex space-x-4">
                        <div class="flex-1">
                            <label for="jaTrabalhou" class="block font-medium">Já trabalhou?</label>
                            <select id="jaTrabalhou" name="jaTrabalhou" x-model="trabalhou"
                                class="w-full border rounded px-3 py-2 text-sm">
                                <option value="">Selecione</option>
                                <option value="sim">Sim</option>
                                <option value="nao">Não</option>
                            </select>
                        </div>

                        <div class="flex-1" x-show="trabalhou === 'sim'" x-transition>
                            <label for="carteiraTrabalho" class="block font-medium">Tem CT?</label>
                            <select id="carteiraTrabalho" name="carteiraTrabalho" x-model="temCT"
                                class="w-full border rounded px-3 py-2 text-sm">
                                <option value="">Selecione</option>
                                <option value="sim">Sim</option>
                                <option value="nao">Não</option>
                            </select>
                        </div>

                        <div class="flex-1" x-show="temCT === 'sim'" x-transition>
                            <label for="ctpsAssinada" class="block font-medium">CT assinada?</label>
                            <select id="ctpsAssinada" name="ctpsAssinada" x-model="ctAssinada"
                                class="w-full border rounded px-3 py-2 text-sm">
                                <option value="">Selecione</option>
                                <option value="sim">Sim</option>
                                <option value="nao">Não</option>
                            </select>
                        </div>
                    </div>

                    <!-- Qual Função -->
                    <div class="mt-4" x-show="ctAssinada === 'sim'" x-transition>
                        <label for="qualFuncao" class="block font-medium">Qual Função?</label>
                        <input type="text" id="qualFuncao" name="qualFuncao" value="{{ old('qualFuncao') }}"
                            class="w-full border rounded px-3 py-2 text-sm">
                    </div>

                </div>
            </div>

            <!-- STEP 2: ENDEREÇO -->
            <div x-show="step === 2" x-cloak x-transition>
                <h2 class="text-xl font-bold mb-4 text-brand border-b-2 border-brand pb-1">2 - ENDEREÇO</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="text" id="cep" name="cep" value="{{ old('cep') }}" placeholder="CEP" class="border rounded p-2">
                    <input type="text" id="rua" name="rua" value="{{ old('rua') }}" placeholder="Rua/Av." class="border rounded p-2">
                    <input type="text" id="numero" name="numero" value="{{ old('numero') }}" placeholder="N°" class="border rounded p-2">
                    <input type="text" id="complemento" name="complemento" value="{{ old('complemento') }}" placeholder="Compl." class="border rounded p-2">
                    <input type="text" id="bairro" name="bairro" value="{{ old('bairro') }}" placeholder="Bairro" class="border rounded p-2">
                    <input type="text" id="cidade" name="cidade" value="{{ old('cidade') }}" placeholder="Cidade" class="border rounded p-2">
                    <select id="uf" name="uf" class="border rounded p-2">
                        <option value="">UF</option>
                        @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                        <option value="{{ $uf }}" {{ old('uf') === $uf ? 'selected' : '' }}>{{ $uf }}</option>
                        @endforeach
                    </select>
                    <input type="text" id="telefone" name="telefone" value="{{ old('telefone') }}" placeholder="Tel." class="border rounded p-2">
                    <input type="text" id="celular" name="celular" value="{{ old('celular') }}" placeholder="Cel." class="border rounded p-2">
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="E-mail" class="md:col-span-2 border rounded p-2">
                </div>
            </div>

            <div x-show="step === 3" x-cloak x-transition>
                <h2 class="text-xl font-bold mb-4 text-brand border-b-2 border-brand pb-1">3 - ESCOLARIDADE</h2>

                <div x-data="{ concluido: '{{ old('concluido') }}' }" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="text" id="escola" name="escola" value="{{ old('escola') }}" placeholder="Escola" class="border rounded p-2">

                    <select id="ano" name="ano" class="border rounded p-2">
                        <option value="">Ano</option>
                        <option value="1º" {{ old('ano') === '1º' ? 'selected' : '' }}>1º</option>
                        <option value="2º" {{ old('ano') === '2º' ? 'selected' : '' }}>2º</option>
                        <option value="3º" {{ old('ano') === '3º' ? 'selected' : '' }}>3º</option>
                        <option value="9º" {{ old('ano') === '9º' ? 'selected' : '' }}>9º</option>
                    </select>

                    <div class="md:col-span-2">
                        <input type="hidden" name="concluido" value="0">

                        <label for="concluido" class="block font-medium">Concluído?</label>
                        <div>
                            <label class="inline-flex items-center">
                                <input type="radio" name="concluido" value="0" class="form-radio" x-model="concluido">
                                <span class="ml-2">Não</span>
                            </label>
                            <label class="inline-flex items-center ml-6">
                                <input type="radio" name="concluido" value="1" class="form-radio" x-model="concluido">
                                <span class="ml-2">Sim</span>
                            </label>
                        </div>
                    </div>


                    <input type="text" id="periodo" name="periodo" value="{{ old('periodo') }}" placeholder="Período"
                        class="border rounded p-2" x-show="concluido === '0'" x-cloak>

                    <input type="text" id="anoConclusao" name="anoConclusao" value="{{ old('anoConclusao') }}" placeholder="Ano de Conclusão"
                        class="border rounded p-2" x-show="concluido === '1'" x-cloak>

                    <input type="text" id="cursoAtual" name="cursoAtual" value="{{ old('cursoAtual') }}" placeholder="Curso Atual"
                        class="md:col-span-2 border rounded p-2" x-show="concluido === '1'" x-cloak>
                </div>
            </div>
            <!-- Step 4 - Dados Socioeconômicos -->
            <div x-show="step === 4" x-cloak x-transition>
                <h2 class="text-xl font-bold mb-4 text-brand border-b-2 border-brand pb-1">4- DADOS SOCIOECONÔMICOS</h2>

                <!-- Moradia -->
                <div class="mb-4" x-data="{ moradia: '' }">
                    <label class="font-semibold">Moradia:</label><br>
                    <label><input type="radio" name="moradia" value="Própria" x-model="moradia"> Própria</label>
                    <label><input type="radio" name="moradia" value="Cedida" x-model="moradia"> Cedida</label>
                    <label><input type="radio" name="moradia" value="Alugada" x-model="moradia"> Alugada</label>
                    <label><input type="radio" name="moradia" value="Financiada" x-model="moradia"> Financiada</label>

                    <!-- Input aparece só se Alugada ou Financiada -->
                    <input type="text" name="moradia_porquem" placeholder="Por quem?" class="border rounded p-1 mt-1 w-full" x-show="moradia === 'Cedida'">
                </div>

                <!-- Benefícios Sociais -->
                <div class="mb-4" x-data="{ beneficio: '' }">
                    <label class="font-semibold">A família recebe algum benefício social?</label><br>
                    <label><input type="radio" name="beneficio" value="Sim" x-model="beneficio"> Sim</label>
                    <label><input type="radio" name="beneficio" value="Não" x-model="beneficio"> Não</label>

                    <!-- Campos aparecem só se Sim -->
                    <div class="mt-2 grid grid-cols-2 gap-2" x-show="beneficio === 'Sim'">
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
                    <div x-data="{ convenio: '' }">
                        <label class="block font-medium">Possui convênio médico?</label>
                        <div class="flex gap-4 mt-1">
                            <label><input type="radio" name="convenio" value="sim" x-model="convenio"> Sim</label>
                            <label><input type="radio" name="convenio" value="nao" x-model="convenio"> Não</label>
                        </div>
                        <input type="text" name="qual_convenio" placeholder="Se sim, qual?"
                            class="mt-1 block w-full border rounded p-2"
                            x-show="convenio === 'sim'" x-transition>
                    </div>

                    <!-- Vacinação -->
                    <div x-data="{ vacinacao: '' }">
                        <label class="block font-medium">A vacinação está em dia?</label>
                        <div class="flex gap-4 mt-1">
                            <label><input type="radio" name="vacinacao" value="sim" x-model="vacinacao"> Sim</label>
                            <label><input type="radio" name="vacinacao" value="nao" x-model="vacinacao"> Não</label>
                        </div>
                    </div>

                    <!-- Queixa de saúde -->
                    <div x-data="{ queixa: '' }">
                        <label class="block font-medium">Apresenta alguma queixa de saúde no momento?</label>
                        <div class="flex gap-4 mt-1">
                            <label><input type="radio" name="queixa_saude" value="sim" x-model="queixa"> Sim</label>
                            <label><input type="radio" name="queixa_saude" value="nao" x-model="queixa"> Não</label>
                        </div>
                        <input type="text" name="qual_queixa" placeholder="Se sim, qual?"
                            class="mt-1 block w-full border rounded p-2"
                            x-show="queixa === 'sim'" x-transition>
                    </div>

                    <!-- Alergia -->
                    <div x-data="{ alergia: '' }">
                        <label class="block font-medium">Possui alguma alergia?</label>
                        <div class="flex gap-4 mt-1">
                            <label><input type="radio" name="alergia" value="sim" x-model="alergia"> Sim</label>
                            <label><input type="radio" name="alergia" value="nao" x-model="alergia"> Não</label>
                        </div>
                        <input type="text" name="qual_alergia" placeholder="Se sim, qual?"
                            class="mt-1 block w-full border rounded p-2"
                            x-show="alergia === 'sim'" x-transition>
                    </div>

                    <!-- Tratamento -->
                    <div x-data="{ tratamento: '' }">
                        <label class="block font-medium">Já fez ou faz algum tipo de tratamento?</label>
                        <div class="flex gap-4 mt-1">
                            <label><input type="radio" name="tratamento" value="sim" x-model="tratamento"> Sim</label>
                            <label><input type="radio" name="tratamento" value="nao" x-model="tratamento"> Não</label>
                        </div>
                        <input type="text" name="qual_tratamento" placeholder="Se sim, qual?"
                            class="mt-1 block w-full border rounded p-2"
                            x-show="tratamento === 'sim'" x-transition>
                    </div>

                    <!-- Uso regular de remédio -->
                    <div x-data="{ remedio: '' }">
                        <label class="block font-medium">Faz uso regular de algum remédio?</label>
                        <div class="flex gap-4 mt-1">
                            <label><input type="radio" name="uso_remedio" value="sim" x-model="remedio"> Sim</label>
                            <label><input type="radio" name="uso_remedio" value="nao" x-model="remedio"> Não</label>
                        </div>
                        <input type="text" name="qual_remedio" placeholder="Se sim, qual?"
                            class="mt-1 block w-full border rounded p-2"
                            x-show="remedio === 'sim'" x-transition>
                    </div>

                    <!-- Cirurgia -->
                    <div x-data="{ cirurgia: '' }">
                        <label class="block font-medium">Já fez alguma cirurgia?</label>
                        <div class="flex gap-4 mt-1">
                            <label><input type="radio" name="cirurgia" value="sim" x-model="cirurgia"> Sim</label>
                            <label><input type="radio" name="cirurgia" value="nao" x-model="cirurgia"> Não</label>
                        </div>
                        <input type="text" name="motivo_cirurgia" placeholder="Se sim, qual o motivo?"
                            class="mt-1 block w-full border rounded p-2"
                            x-show="cirurgia === 'sim'" x-transition>
                    </div>

                    <div x-data="{ pcd: '' }">
                        <!-- PCD -->
                        <label class="block font-medium">É PCD (Pessoa Com Deficiência)?</label>
                        <div class="flex gap-4 mt-1">
                            <label><input type="radio" name="pcd" value="sim" x-model="pcd"> Sim</label>
                            <label><input type="radio" name="pcd" value="nao" x-model="pcd"> Não</label>
                        </div>

                        <!-- Se sim, qual? -->
                        <div x-show="pcd === 'sim'" x-transition>
                            <label class="block font-medium">Se sim, qual?</label>
                            <input type="text" name="qual_pcd" class="mt-1 block w-full border rounded p-2">
                        </div>

                        <!-- Em função disso, possui alguma necessidade especial? -->
                        <div x-show="pcd === 'sim'" x-transition>
                            <label class="block font-medium">Em função disso, possui alguma necessidade especial?</label>
                            <input type="text" name="necessidade_especial" class="mt-1 block w-full border rounded p-2">
                        </div>
                    </div>


                    <!-- Doença congênita/hereditária -->
                    <div x-data="{ doenca: '' }">
                        <label class="block font-medium">Tem alguma doença congênita e/ou hereditária?</label>
                        <div class="flex gap-4 mt-1">
                            <label><input type="radio" name="doenca_congenita" value="sim" x-model="doenca"> Sim</label>
                            <label><input type="radio" name="doenca_congenita" value="nao" x-model="doenca"> Não</label>
                        </div>
                        <input type="text" name="qual_doenca_congenita" placeholder="Se sim, qual?"
                            class="mt-1 block w-full border rounded p-2"
                            x-show="doenca === 'sim'" x-transition>
                    </div>

                    <!-- Psicólogo/Psiquiatra -->
                    <div x-data="{ psicologo: '' }">
                        <label class="block font-medium">Está passando com psicólogo e/ou psiquiatra ou já passou?</label>
                        <div class="flex gap-4 mt-1">
                            <label><input type="radio" name="psicologo" value="sim" x-model="psicologo"> Sim</label>
                            <label><input type="radio" name="psicologo" value="nao" x-model="psicologo"> Não</label>
                        </div>
                        <input type="text" name="quando_psicologo" placeholder="Se sim, quando?"
                            class="mt-1 block w-full border rounded p-2"
                            x-show="psicologo === 'sim'" x-transition>
                    </div>

                    <!-- Convulsões / epilepsia / desmaios -->
                    <div x-data="{ convulsao: '' }">
                        <label class="block font-medium">Tem ou já teve convulsões, epilepsia ou desmaios?</label>
                        <div class="flex gap-4 mt-1">
                            <label><input type="radio" name="convulsao" value="sim" x-model="convulsao"> Sim</label>
                            <label><input type="radio" name="convulsao" value="nao" x-model="convulsao"> Não</label>
                        </div>
                        <input type="text" name="quando_convulsao" placeholder="Se sim, quando?"
                            class="mt-1 block w-full border rounded p-2"
                            x-show="convulsao === 'sim'" x-transition>
                    </div>

                    <!-- Histórico familiar de doenças -->
                    <div x-data="{ familiaDoenca: '' }" class="md:col-span-2">
                        <label class="block font-medium">Algum membro da família possui alguma doença congênita e/ou hereditária? (Ex. Hipertensão, hipotireoidismo, diabetes, outros)</label>
                        <div class="flex gap-4 mt-1">
                            <label><input type="radio" name="familia_doenca" value="sim" x-model="familiaDoenca"> Sim</label>
                            <label><input type="radio" name="familia_doenca" value="nao" x-model="familiaDoenca"> Não</label>
                        </div>
                        <input type="text" name="qual_familia_doenca" placeholder="Se sim, quem?"
                            class="mt-1 block w-full border rounded p-2"
                            x-show="familiaDoenca === 'sim'" x-transition>
                    </div>

                    <!-- Medicamentos e acompanhamento -->
                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">

                        <div x-data="{ familiaDepressao: '' }">
                            <label class="block font-medium">Algum membro da família faz uso de medicamentos para sintomas depressivos ou ansiosos?</label>
                            <div class="flex gap-4 mt-1">
                                <label><input type="radio" name="familia_depressao" value="sim" x-model="familiaDepressao"> Sim</label>
                                <label><input type="radio" name="familia_depressao" value="nao" x-model="familiaDepressao"> Não</label>
                            </div>
                            <input type="text" name="quem_familia_depressao" placeholder="Se sim, quem?"
                                class="mt-1 block w-full border rounded p-2"
                                x-show="familiaDepressao === 'sim'" x-transition>
                        </div>

                        <div x-data="{ medicoEspecialista: '' }">
                            <label class="block font-medium">Está passando com algum médico especialista?</label>
                            <div class="flex gap-4 mt-1">
                                <label><input type="radio" name="medico_especialista" value="sim" x-model="medicoEspecialista"> Sim</label>
                                <label><input type="radio" name="medico_especialista" value="nao" x-model="medicoEspecialista"> Não</label>
                            </div>
                            <input type="text" name="qual_medico_especialista" placeholder="Se sim, qual?"
                                class="mt-1 block w-full border rounded p-2"
                                x-show="medicoEspecialista === 'sim'" x-transition>
                        </div>

                        <div x-data="{ familiaPsicologico: '' }">
                            <label class="block font-medium">Algum membro da família faz acompanhamento psicológico?</label>
                            <div class="flex gap-4 mt-1">
                                <label><input type="radio" name="familia_psicologico" value="sim" x-model="familiaPsicologico"> Sim</label>
                                <label><input type="radio" name="familia_psicologico" value="nao" x-model="familiaPsicologico"> Não</label>
                            </div>
                            <input type="text" name="quem_familia_psicologico" placeholder="Se sim, quem?"
                                class="mt-1 block w-full border rounded p-2"
                                x-show="familiaPsicologico === 'sim'" x-transition>
                        </div>

                        <div x-data="{ familiaAlcool: '' }">
                            <label class="block font-medium">Algum membro da família faz uso abusivo de bebida alcoólica?</label>
                            <div class="flex gap-4 mt-1">
                                <label><input type="radio" name="familia_alcool" value="sim" x-model="familiaAlcool"> Sim</label>
                                <label><input type="radio" name="familia_alcool" value="nao" x-model="familiaAlcool"> Não</label>
                            </div>
                            <input type="text" name="quem_familia_alcool" placeholder="Se sim, quem?"
                                class="mt-1 block w-full border rounded p-2"
                                x-show="familiaAlcool === 'sim'" x-transition>
                        </div>

                        <div x-data="{ familiaDrogas: '' }">
                            <label class="block font-medium">Algum membro da família faz uso abusivo de drogas?</label>
                            <div class="flex gap-4 mt-1">
                                <label><input type="radio" name="familia_drogas" value="sim" x-model="familiaDrogas"> Sim</label>
                                <label><input type="radio" name="familia_drogas" value="nao" x-model="familiaDrogas"> Não</label>
                            </div>
                            <input type="text" name="quem_familia_drogas" placeholder="Se sim, quem?"
                                class="mt-1 block w-full border rounded p-2"
                                x-show="familiaDrogas === 'sim'" x-transition>
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
   
<div x-data="{ open: {{ session('success') ? 'true' : 'false' }} }">
    
    <!-- Modal -->
    <div 
        x-show="open" 
        x-transition
        class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50"
    >
        <div 
            @click.away="open = false" 
            class="bg-white rounded-xl shadow-lg max-w-lg w-full p-6 relative"
        >
            <!-- Fechar -->
            <button 
                @click="open = false" 
                class="absolute top-3 right-3 text-gray-500 hover:text-gray-800"
            >
                &times;
            </button>

            <!-- Conteúdo do Modal -->
            <h2 class="text-xl font-semibold mb-4">Sucesso!</h2>
            <p>{{ session('success') }}</p>

            <div class="flex justify-end mt-4">
                <button 
                    @click="open = false" 
                    class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
                >
                    Fechar
                </button>
            </div>
        </div>
    </div>
</div>

    <script>
        window.wizard = function() {
            return {
                // ======= Wizard =======
                step: 1,
                nextStep() {
                    if (this.step < 7) this.step++;
                },
                prevStep() {
                    if (this.step > 1) this.step--;
                },

                // ======= Assinatura =======
                desenho: false,
                lastX: 0,
                lastY: 0,
                canvas: null,
                ctx: null,
                inputAssinatura: null,

                iniciarAssinatura(e) {
                    this.desenho = true;
                    const pos = this.getPos(e);
                    this.lastX = pos.x;
                    this.lastY = pos.y;
                },
                moverAssinatura(e) {
                    if (!this.desenho) return;
                    const pos = this.getPos(e);
                    this.ctx.beginPath();
                    this.ctx.moveTo(this.lastX, this.lastY);
                    this.ctx.lineTo(pos.x, pos.y);
                    this.ctx.stroke();
                    this.lastX = pos.x;
                    this.lastY = pos.y;
                },
                terminarAssinatura() {
                    this.desenho = false;
                    this.salvarAssinatura();
                },
                limparAssinatura() {
                    if (this.ctx && this.canvas) {
                        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                        if (this.inputAssinatura) this.inputAssinatura.value = "";
                    }
                },
                salvarAssinatura() {
                    if (this.inputAssinatura && this.canvas)
                        this.inputAssinatura.value = this.canvas.toDataURL("image/png");
                },
                getPos(e) {
                    const rect = this.canvas.getBoundingClientRect();
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
                },
                initCanvas() {
                    this.canvas = document.getElementById('assinaturaCanvas');
                    this.inputAssinatura = document.getElementById('assinaturaInput');
                    if (!this.canvas) return;
                    this.ctx = this.canvas.getContext('2d');
                    this.ctx.lineWidth = 2;
                    this.ctx.lineCap = 'round';
                    this.ctx.strokeStyle = '#000';

                    // Eventos mouse
                    this.canvas.addEventListener('mousedown', e => this.iniciarAssinatura(e));
                    this.canvas.addEventListener('mousemove', e => this.moverAssinatura(e));
                    this.canvas.addEventListener('mouseup', () => this.terminarAssinatura());
                    this.canvas.addEventListener('mouseleave', () => this.terminarAssinatura());

                    // Eventos touch
                    this.canvas.addEventListener('touchstart', e => this.iniciarAssinatura(e));
                    this.canvas.addEventListener('touchmove', e => this.moverAssinatura(e));
                    this.canvas.addEventListener('touchend', () => this.terminarAssinatura());
                },

                // ======= Máscaras e idade =======
                initMasks() {
                    const cpf = document.getElementById("cpf");
                    if (cpf) IMask(cpf, {
                        mask: "000.000.000-00"
                    });
                    const rg = document.getElementById("rg");
                    if (rg) IMask(rg, {
                        mask: "00.000.000-0"
                    });
                    const cep = document.getElementById("cep");
                    if (cep) IMask(cep, {
                        mask: "00000-000"
                    });
                },
                calcularIdade() {
                    const nascEl = document.getElementById("dataNascimento");
                    const idadeEl = document.getElementById("idade");
                    if (!nascEl || !idadeEl || !nascEl.value) return;
                    const nasc = new Date(nascEl.value);
                    const hoje = new Date();
                    let idade = hoje.getFullYear() - nasc.getFullYear();
                    const m = hoje.getMonth() - nasc.getMonth();
                    if (m < 0 || (m === 0 && hoje.getDate() < nasc.getDate())) idade--;
                    idadeEl.value = idade;
                },

                // ======= Busca CEP =======
                debounce: null,
                buscarCEP() {
                    const cepEl = document.getElementById("cep");
                    if (!cepEl) return;
                    const cep = cepEl.value.replace(/\D/g, '');
                    if (cep.length !== 8) return this.limparCEP();

                    fetch(`https://viacep.com.br/ws/${cep}/json/`)
                        .then(res => res.json())
                        .then(data => {
                            const campos = ["rua", "bairro", "cidade", "uf", "complemento"];
                            if (!data.erro) {
                                campos.forEach(campo => {
                                    const el = document.getElementById(campo);
                                    if (el) {
                                        if (campo === "rua") el.value = data.logradouro || "";
                                        else if (campo === "cidade") el.value = data.localidade || "";
                                        else el.value = data[campo] || "";
                                    }
                                });
                            } else this.limparCEP();
                        }).catch(err => console.error(err));
                },
                limparCEP() {
                    ["rua", "bairro", "cidade", "uf", "complemento"].forEach(campo => {
                        const el = document.getElementById(campo);
                        if (el) el.value = "";
                    });
                },

                // ======= Inicialização =======
                init() {
                    this.initMasks();
                    this.initCanvas();

                    // Listener CEP
                    const cepEl = document.getElementById("cep");
                    if (cepEl) {
                        cepEl.addEventListener("input", () => {
                            clearTimeout(this.debounce);
                            this.debounce = setTimeout(() => this.buscarCEP(), 300);
                        });
                    }

                    // Listener idade
                    const nascEl = document.getElementById("dataNascimento");
                    if (nascEl) {
                        nascEl.addEventListener("change", () => this.calcularIdade());
                    }
                }
            }
        }

        // ======= Família / Tabela de Familiares =======
        function familyTable() {
            return {
                rows: [],
                addRow() {
                    this.rows.push({
                        parentesco: "",
                        nomeCompleto: "",
                        idade: "",
                        profissao: "",
                        empresa: "",
                        salarioBase: ""
                    });
                    this.updateInput();
                },
                removeRow(index) {
                    this.rows.splice(index, 1);
                    this.updateInput();
                },
                updateInput() {
                    const input = document.getElementById("familiaresInput");
                    if (input) input.value = JSON.stringify(this.rows);
                },
                init() {
                    const input = document.getElementById("familiaresInput");
                    if (input && input.value) {
                        try {
                            this.rows = JSON.parse(input.value);
                        } catch (e) {
                            this.rows = [];
                        }
                    }
                }
            }
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
            Swal.fire({
                title: "{{ session('success') }}",
                icon: 'success',
                confirmButtonText: 'OK',
                backdrop: true,
                allowOutsideClick: false,
                customClass: {
                    popup: 'rounded-xl shadow-lg border-2 border-brand',
                    title: 'text-xl font-bold text-brand',
                    confirmButton: 'bg-brand hover:bg-brand-dark text-white font-semibold px-6 py-2 rounded-lg'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('welcome') }}";
                }
            });
            @elseif(session('error'))
            Swal.fire({
                title: "{{ session('error') }}",
                icon: 'error',
                confirmButtonText: 'OK',
                backdrop: true,
                allowOutsideClick: false,
                customClass: {
                    popup: 'rounded-xl shadow-lg border-2 border-red-500',
                    title: 'text-xl font-bold text-red-500',
                    confirmButton: 'bg-red-500 hover:bg-red-600 text-white font-semibold px-6 py-2 rounded-lg'
                }
            });
            @endif
        });
    </script>
</x-guest-layout>