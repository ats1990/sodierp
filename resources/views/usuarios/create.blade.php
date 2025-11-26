<x-guest-layout>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/imask"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div x-data="wizard()" class="w-full max-w-4xl mx-auto p-8 bg-white rounded-xl shadow-lg space-y-6">
        <form id="usuarioForm" action="{{ route('usuarios.store') }}" method="POST">
            @csrf

            {{-- ===================== STEP 1: Dados Pessoais ===================== --}}
            <div x-show="step === 1" x-cloak x-transition>
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
                    1 - Dados Pessoais
                </h2>

                <div class="space-y-4">
                    <div class="flex space-x-4">
                        <div class="flex-1">
                            <label for="nomeCompleto" class="block font-medium">Nome completo</label>
                            <input type="text" id="nomeCompleto" name="nomeCompleto"
                                   value="{{ old('nomeCompleto') }}"
                                   class="w-full border rounded px-3 py-2 text-sm">
                        </div>
                        <div class="flex-1">
                            <label for="nomeSocial" class="block font-medium">Nome Social</label>
                            <input type="text" id="nomeSocial" name="nomeSocial"
                                   value="{{ old('nomeSocial') }}"
                                   class="w-full border rounded px-3 py-2 text-sm">
                        </div>
                    </div>

                    <div class="flex space-x-4">
                        <div class="flex-1">
                            <label for="dataNascimento" class="block font-medium">Data de Nascimento</label>
                            <input type="date" id="dataNascimento" name="dataNascimento"
                                   value="{{ old('dataNascimento') }}"
                                   class="w-full border rounded px-3 py-2 text-sm">
                        </div>
                        <div class="flex-1">
                            <label for="cpf" class="block font-medium">CPF</label>
                            <input type="text" id="cpf" name="cpf"
                                   value="{{ old('cpf') }}"
                                   class="w-full border rounded px-3 py-2 text-sm">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block font-medium">E-mail</label>
                        <input type="email" id="email" name="email"
                               value="{{ old('email') }}"
                               class="w-full border rounded px-3 py-2 text-sm">
                    </div>

                    <div>
                        <label for="tipo" class="block font-medium">Tipo de Usuário</label>
                        <select id="tipo" name="tipo" x-model="tipo"
                                @change="tipo !== 'professor' ? clearProgramas() : null"
                                class="w-full border rounded px-3 py-2 text-sm">
                            <option value="">Selecione</option>
                            <option value="professor" {{ old('tipo')=='professor'?'selected':'' }}>Professor</option>
                            <option value="coordenacao" {{ old('tipo')=='coordenacao'?'selected':'' }}>Coordenação</option>
                            <option value="administracao" {{ old('tipo')=='administracao'?'selected':'' }}>Administração</option>
                            <option value="psicologo" {{ old('tipo')=='psicologo'?'selected':'' }}>Psicólogo</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- ===================== STEP 2: Programas e Disciplinas ===================== --}}
            <div x-show="step === 2" x-cloak x-transition>
                <h2 class="text-xl font-bold mb-4 text-brand border-b-2 border-brand pb-1">
                    2 - Programas e Disciplinas
                </h2>

                {{-- Programas --}}
                <div class="mb-6">
                    <label class="block font-bold mb-2">Selecione os Programas:</label>
                    <div class="flex flex-wrap gap-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="programa_basica" value="1"
                                   x-model="programas.basica" class="form-checkbox"
                                   {{ old('programa_basica')?'checked':'' }}>
                            <span class="ml-2">Formação Básica</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="programa_aprendizagem" value="1"
                                   x-model="programas.aprendizagem" class="form-checkbox"
                                   {{ old('programa_aprendizagem')?'checked':'' }}>
                            <span class="ml-2">Aprendizagem</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="programa_convivencia" value="1"
                                   x-model="programas.convivencia" class="form-checkbox"
                                   {{ old('programa_convivencia')?'checked':'' }}>
                            <span class="ml-2">Serviço de Convivência</span>
                        </label>
                    </div>
                </div>

                {{-- Disciplinas Formação Básica --}}
                <div x-show="programas.basica" x-cloak x-transition class="mb-4 p-4 border rounded-lg">
                    <h3 class="font-bold mb-2 text-lg">Disciplinas Formação Básica</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @php
                            $disciplinasBasica = ['Teatro','Orientação Pessoal e Profissional','Ética e Cidadania','Administração','Recursos Humanos','Logística','Comunicação','Matemática','Saúde e Bem Estar','Informática'];
                        @endphp
                        @foreach($disciplinasBasica as $disc)
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="disciplinas_basica[]" value="{{ $disc }}" class="form-checkbox"
                                   {{ is_array(old('disciplinas_basica')) && in_array($disc, old('disciplinas_basica'))?'checked':'' }}>
                            <span class="ml-2">{{ $disc }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Disciplinas Aprendizagem --}}
                <div x-show="programas.aprendizagem" x-cloak x-transition class="mb-4 p-4 border rounded-lg">
                    <h3 class="font-bold mb-2 text-lg">Disciplinas Aprendizagem</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @php
                            $disciplinasAprendizagem = ['Mundo do Trabalho','Teatro','Orientação Pessoal e Profissional','Gestão de Projetos','Informática','Gestão de Pessoas','Saúde e Bem Estar','Gestão Institucional','Comunicação','Ética e Cidadania','Empreendedorismo'];
                        @endphp
                        @foreach($disciplinasAprendizagem as $disc)
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="disciplinas_aprendizagem[]" value="{{ $disc }}" class="form-checkbox"
                                   {{ is_array(old('disciplinas_aprendizagem')) && in_array($disc, old('disciplinas_aprendizagem'))?'checked':'' }}>
                            <span class="ml-2">{{ $disc }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Disciplinas Serviço de Convivência --}}
                <div x-show="programas.convivencia" x-cloak x-transition class="mb-4 p-4 border rounded-lg">
                    <h3 class="font-bold mb-2 text-lg">Disciplinas Serviço de Convivência</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @php
                            $disciplinasConvivencia = ['Informática','Fotografia','Teatro','Mundo do Trabalho'];
                        @endphp
                        @foreach($disciplinasConvivencia as $disc)
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="disciplinas_convivencia[]" value="{{ $disc }}" class="form-checkbox"
                                   {{ is_array(old('disciplinas_convivencia')) && in_array($disc, old('disciplinas_convivencia'))?'checked':'' }}>
                            <span class="ml-2">{{ $disc }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ===================== STEP 3: Senha ===================== --}}
            <div x-show="step === 3" x-cloak x-transition>
                <h2 class="text-xl font-bold mb-4 text-brand border-b-2 border-brand pb-1">3 - Definir Senha</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block font-medium">Criar senha</label>
                        <input type="password" id="password" name="password" autocomplete="new-password"
                               class="w-full border rounded px-3 py-2 text-sm" placeholder="mín. 6 caracteres">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block font-medium">Repetir senha</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
                               class="w-full border rounded px-3 py-2 text-sm" placeholder="repita a senha">
                    </div>
                </div>
            </div>

            {{-- ===================== Buttons ===================== --}}
            <div class="flex justify-between mt-6">
                <button type="button" @click="prevStep()" x-show="step>1" class="px-4 py-2 bg-gray-500 text-white rounded">Anterior</button>
                <button type="button" @click="nextStep()" x-show="step<3" class="px-4 py-2 bg-flamingo text-white rounded hover:bg-brand-dark">Próximo</button>
                <button type="submit" x-show="step===3" class="px-4 py-2 bg-flamingo text-white rounded hover:bg-brand-dark">Cadastrar Usuário</button>
            </div>
        </form>
    </div>

    {{-- ===================== Wizard Script ===================== --}}
    <script>
        window.wizard = function(){
            return {
                step: 1,
                tipo: '{{ old("tipo") ?? "" }}',
                programas: {
                    basica: {{ old('programa_basica')?'true':'false' }},
                    aprendizagem: {{ old('programa_aprendizagem')?'true':'false' }},
                    convivencia: {{ old('programa_convivencia')?'true':'false' }},
                },

                nextStep() {
                    const nome = document.getElementById('nomeCompleto');
                    const email = document.getElementById('email');
                    const password = document.getElementById('password');
                    const passwordConfirm = document.getElementById('password_confirmation');

                    if(this.step === 1){
                        if(!nome.value.trim()){ Swal.fire('Atenção','O campo Nome Completo é obrigatório.','warning'); return; }
                        if(!email.value.trim()){ Swal.fire('Atenção','O campo E-mail é obrigatório.','warning'); return; }

                        if(this.tipo !== 'professor'){
                            this.step = 3;
                            return;
                        }
                    }

                    if(this.step === 2){
                        this.step++;
                    }

                    if(this.step === 3){
                        if(!password.value){ Swal.fire('Atenção','O campo Senha é obrigatório.','warning'); return; }
                        if(password.value.length<6){ Swal.fire('Atenção','A senha deve ter no mínimo 6 caracteres.','warning'); return; }
                        if(password.value!==passwordConfirm.value){ Swal.fire('Atenção','As senhas não conferem.','warning'); return; }
                    }

                    if(this.step < 3 && (this.step !== 2 || this.tipo === 'professor')){
                        this.step++;
                    }
                },

                prevStep(){
                    if(this.step > 1){
                        if(this.step === 3 && this.tipo !== 'professor'){
                            this.step = 1;
                        } else {
                            this.step--;
                        }
                    }
                },

                clearProgramas(){
                    this.programas.basica = false;
                    this.programas.aprendizagem = false;
                    this.programas.convivencia = false;
                },

                initMasks(){
                    const cpf = document.getElementById('cpf');
                    if(cpf) IMask(cpf,{mask:'000.000.000-00'});
                },

                init(){
                    this.initMasks();
                }
            }
        }
    </script>
</x-guest-layout>
