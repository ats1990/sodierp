@extends('layouts.app')

@section('content')
<div class="container">
    <form action="{{ route('ficha.store') }}" method="POST">
        @csrf

        <!-- 1- DADOS PESSOAIS DO(A) JOVEM -->
        <h2>1- DADOS PESSOAIS DO(A) JOVEM</h2>
        <div class="form-group">
            <label for="nomeCompleto">Nome completo:</label>
            <input type="text" id="nomeCompleto" name="nomeCompleto" required>
        </div>
        <div class="form-group">
            <label for="nomeSocial">Nome social:</label>
            <input type="text" id="nomeSocial" name="nomeSocial">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="dataNascimento">Data de nascimento:</label>
                <input type="date" id="dataNascimento" name="dataNascimento" required>
            </div>
            <div class="form-group">
                <label for="idade">Idade:</label>
                <input type="number" id="idade" name="idade" min="0" max="120" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="cpf">CPF:</label>
                <input type="text" id="cpf" name="cpf" pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" placeholder="000.000.000-00" required>
            </div>
            <div class="form-group">
                <label for="rg">RG:</label>
                <input type="text" id="rg" name="rg" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Tem Carteira de Trabalho?</label>
                <div class="radio-group">
                    <input type="radio" id="ctpsSim" name="carteiraTrabalho" value="Sim" required>
                    <label for="ctpsSim">Sim</label>
                    <input type="radio" id="ctpsNao" name="carteiraTrabalho" value="Não">
                    <label for="ctpsNao">Não</label>
                </div>
            </div>
            <div class="form-group">
                <label>Já trabalhou?</label>
                <div class="radio-group">
                    <input type="radio" id="jaTrabalhouSim" name="jaTrabalhou" value="Sim" required>
                    <label for="jaTrabalhouSim">Sim</label>
                    <input type="radio" id="jaTrabalhouNao" name="jaTrabalhou" value="Não">
                    <label for="jaTrabalhouNao">Não</label>
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Carteira de Trabalho assinada?</label>
                <div class="radio-group">
                    <input type="radio" id="ctpsAssinadaSim" name="ctpsAssinada" value="Sim">
                    <label for="ctpsAssinadaSim">Sim</label>
                    <input type="radio" id="ctpsAssinadaNao" name="ctpsAssinada" value="Não">
                    <label for="ctpsAssinadaNao">Não</label>
                </div>
            </div>
            <div class="form-group">
                <label for="qualFuncao">Qual função?</label>
                <input type="text" id="qualFuncao" name="qualFuncao">
            </div>
        </div>

        <!-- 2- ENDEREÇO -->
        <h2>2- ENDEREÇO</h2>
        <div class="form-group">
            <label for="ruaAv">Rua/Av.:</label>
            <input type="text" id="ruaAv" name="ruaAv" required>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="numero">N°:</label>
                <input type="text" id="numero" name="numero">
            </div>
            <div class="form-group">
                <label for="complemento">Compl.:</label>
                <input type="text" id="complemento" name="complemento">
            </div>
            <div class="form-group">
                <label for="cep">CEP:</label>
                <input type="text" id="cep" name="cep" pattern="\d{5}-\d{3}" placeholder="00000-000" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="cidade">Cidade:</label>
                <input type="text" id="cidade" name="cidade" required>
            </div>
            <div class="form-group">
                <label for="bairro">Bairro:</label>
                <input type="text" id="bairro" name="bairro" required>
            </div>
            <div class="form-group">
                <label for="uf">UF:</label>
                <select id="uf" name="uf" required>
                    <option value="">Selecione</option>
                    @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $estado)
                        <option value="{{ $estado }}">{{ $estado }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="tel">Tel.:</label>
                <input type="tel" id="tel" name="tel" pattern="\d{2}\s?\d{4,5}-?\d{4}" placeholder="(XX) XXXX-XXXX">
            </div>
            <div class="form-group">
                <label for="cel">Cel.:</label>
                <input type="tel" id="cel" name="cel" pattern="\d{2}\s?\d{5}-?\d{4}" placeholder="(XX) 9XXXX-XXXX" required>
            </div>
            <div class="form-group">
                <label for="rec">Rec.:</label>
                <input type="tel" id="rec" name="rec" pattern="\d{2}\s?\d{4,5}-?\d{4}" placeholder="(XX) XXXX-XXXX">
            </div>
        </div>

        <div class="form-group">
            <label for="email">E-Mail:</label>
            <input type="email" id="email" name="email">
        </div>

        <!-- 3- ESCOLARIDADE -->
        <h2>3- ESCOLARIDADE</h2>
        <div class="form-group">
            <label for="escola">Escola:</label>
            <input type="text" id="escola" name="escola">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Ano:</label>
                <div class="checkbox-group">
                    <input type="radio" id="ano9" name="anoEscolar" value="9º">
                    <label for="ano9">9º</label>
                    <input type="radio" id="ano1" name="anoEscolar" value="1º">
                    <label for="ano1">1º</label>
                    <input type="radio" id="ano2" name="anoEscolar" value="2º">
                    <label for="ano2">2º</label>
                    <input type="radio" id="ano3" name="anoEscolar" value="3º">
                    <label for="ano3">3º</label>
                </div>
            </div>
            <div class="form-group">
                <label for="periodo">Período:</label>
                <input type="text" id="periodo" name="periodo">
            </div>
            <div class="form-group">
                <label>Concluído:</label>
                <div class="radio-group">
                    <input type="radio" id="concluidoSim" name="concluidoEscolaridade" value="Sim">
                    <label for="concluidoSim">Sim</label>
                    <input type="radio" id="concluidoNao" name="concluidoEscolaridade" value="Não">
                    <label for="concluidoNao">Não</label>
                </div>
            </div>
            <div class="form-group">
                <label for="anoConclusao">Ano de conclusão:</label>
                <input type="number" id="anoConclusao" name="anoConclusao" min="1900" max="2100">
            </div>
        </div>

        <div class="form-group">
            <label for="cursoAtual">Está fazendo algum curso:</label>
            <input type="text" id="cursoAtual" name="cursoAtual">
        </div>

        <!-- 4- DADOS SOCIOECONÔMICOS -->
        <h2>4- DADOS SOCIOECONÔMICOS</h2>
        <div class="form-group">
            <label>Moradia:</label>
            <select name="moradia" required>
                <option value="">Selecione</option>
                <option value="Própria">Própria</option>
                <option value="Cedida">Cedida</option>
                <option value="Alugada">Alugada</option>
                <option value="Financiada">Financiada</option>
            </select>
        </div>

        <div class="form-group">
            <label for="quemCedida">Por quem é cedida?</label>
            <input type="text" id="quemCedida" name="quemCedida">
        </div>

        <div class="form-group">
            <label>Recebe benefícios sociais?</label>
            <div class="radio-group">
                <input type="radio" id="beneficioSim" name="recebeBeneficio" value="Sim">
                <label for="beneficioSim">Sim</label>
                <input type="radio" id="beneficioNao" name="recebeBeneficio" value="Não">
                <label for="beneficioNao">Não</label>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="bolsaFamiliaValor">Bolsa Família:</label>
                <input type="number" id="bolsaFamiliaValor" name="bolsaFamiliaValor" min="0" step="0.01">
            </div>
            <div class="form-group">
                <label for="bpcLoasValor">BPC/LOAS:</label>
                <input type="number" id="bpcLoasValor" name="bpcLoasValor" min="0" step="0.01">
            </div>
            <div class="form-group">
                <label for="pensaoAlimenticiaValor">Pensão alimentícia:</label>
                <input type="number" id="pensaoAlimenticiaValor" name="pensaoAlimenticiaValor" min="0" step="0.01">
            </div>
            <div class="form-group">
                <label for="auxAluguelValor">Auxílio aluguel:</label>
                <input type="number" id="auxAluguelValor" name="auxAluguelValor" min="0" step="0.01">
            </div>
            <div class="form-group">
                <label for="rendaCidadaValor">Renda cidadã:</label>
                <input type="number" id="rendaCidadaValor" name="rendaCidadaValor" min="0" step="0.01">
            </div>
            <div class="form-group">
                <label for="outrosBeneficiosValor">Outros:</label>
                <input type="number" id="outrosBeneficiosValor" name="outrosBeneficiosValor" min="0" step="0.01">
            </div>
        </div>

        <!-- Despesas Mensais -->
        <h3>Despesas Mensais</h3>
        <div class="form-row">
            <div class="form-group">
                <label for="agua">Água:</label>
                <input type="number" id="agua" name="agua" min="0" step="0.01">
            </div>
            <div class="form-group">
                <label for="alimentacao">Alimentação:</label>
                <input type="number" id="alimentacao" name="alimentacao" min="0" step="0.01">
            </div>
            <div class="form-group">
                <label for="gás">Gás:</label>
                <input type="number" id="gas" name="gas" min="0" step="0.01">
            </div>
            <div class="form-group">
                <label for="luz">Luz:</label>
                <input type="number" id="luz" name="luz" min="0" step="0.01">
            </div>
            <div class="form-group">
                <label for="medicamento">Medicamento:</label>
                <input type="number" id="medicamento" name="medicamento" min="0" step="0.01">
            </div>
            <div class="form-group">
                <label for="telefoneInternet">Telefone/Internet:</label>
                <input type="number" id="telefoneInternet" name="telefoneInternet" min="0" step="0.01">
            </div>
            <div class="form-group">
                <label for="aluguelFinanciamento">Aluguel/Financiamento:</label>
                <input type="number" id="aluguelFinanciamento" name="aluguelFinanciamento" min="0" step="0.01">
            </div>
        </div>
        <!-- 5- FAMÍLIA -->
        <h2>5- COMPOSIÇÃO FAMILIAR</h2>
        <table class="table table-bordered" id="tabelaFamilia">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Parentesco</th>
                    <th>Idade</th>
                    <th>Escolaridade</th>
                    <th>Profissão</th>
                    <th>Renda</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" name="familia[0][nome]" required></td>
                    <td><input type="text" name="familia[0][parentesco]" required></td>
                    <td><input type="number" name="familia[0][idade]" min="0" required></td>
                    <td><input type="text" name="familia[0][escolaridade]"></td>
                    <td><input type="text" name="familia[0][profissao]"></td>
                    <td><input type="number" name="familia[0][renda]" min="0" step="0.01"></td>
                    <td><button type="button" onclick="removerLinha(this)">Remover</button></td>
                </tr>
            </tbody>
        </table>
        <button type="button" onclick="adicionarLinha()">Adicionar Membro</button>

        <script>
            function adicionarLinha() {
                const table = document.getElementById('tabelaFamilia').getElementsByTagName('tbody')[0];
                const rowCount = table.rows.length;
                const row = table.insertRow();
                row.innerHTML = `
                    <td><input type="text" name="familia[${rowCount}][nome]" required></td>
                    <td><input type="text" name="familia[${rowCount}][parentesco]" required></td>
                    <td><input type="number" name="familia[${rowCount}][idade]" min="0" required></td>
                    <td><input type="text" name="familia[${rowCount}][escolaridade]"></td>
                    <td><input type="text" name="familia[${rowCount}][profissao]"></td>
                    <td><input type="number" name="familia[${rowCount}][renda]" min="0" step="0.01"></td>
                    <td><button type="button" onclick="removerLinha(this)">Remover</button></td>
                `;
            }

            function removerLinha(btn) {
                const row = btn.parentNode.parentNode;
                row.parentNode.removeChild(row);
            }
        </script>

        <!-- 6- SAÚDE -->
        <h2>6- SAÚDE</h2>
        <div class="form-group">
            <label>Possui algum problema de saúde?</label>
            <div class="checkbox-group">
                <input type="checkbox" id="saudeSim" name="problemaSaude[]" value="Sim">
                <label for="saudeSim">Sim</label>
                <input type="checkbox" id="saudeNao" name="problemaSaude[]" value="Não">
                <label for="saudeNao">Não</label>
            </div>
        </div>

        <div class="form-group">
            <label for="qualProblema">Qual problema?</label>
            <input type="text" id="qualProblema" name="qualProblema">
        </div>

        <div class="form-group">
            <label>Acompanhamento médico/psicológico?</label>
            <div class="checkbox-group">
                <input type="checkbox" id="acomMedico" name="acomanhamento[]" value="Médico">
                <label for="acomMedico">Médico</label>
                <input type="checkbox" id="acomPsico" name="acomanhamento[]" value="Psicológico">
                <label for="acomPsico">Psicológico</label>
                <input type="checkbox" id="acomNenhum" name="acomanhamento[]" value="Nenhum">
                <label for="acomNenhum">Nenhum</label>
            </div>
        </div>

        <!-- 7- ASSINATURA E DECLARAÇÃO -->
        <h2>7- ASSINATURA E DECLARAÇÃO</h2>
        <p>Declaro que as informações prestadas são verdadeiras.</p>

        <div class="form-group">
            <label>Assinatura:</label>
            <canvas id="assinatura" style="border:1px solid #000; width:400px; height:150px;"></canvas>
            <input type="hidden" name="assinaturaBase64" id="assinaturaBase64">
            <button type="button" onclick="limparCanvas()">Limpar</button>
        </div>

        <button type="submit">Salvar Ficha</button>
        <button type="reset">Limpar Campos</button>
    </form>
</div>

<script>
    // Canvas assinatura
    const canvas = document.getElementById('assinatura');
    const ctx = canvas.getContext('2d');
    let desenhando = false;

    canvas.addEventListener('mousedown', () => desenhando = true);
    canvas.addEventListener('mouseup', () => desenhando = false);
    canvas.addEventListener('mouseleave', () => desenhando = false);
    canvas.addEventListener('mousemove', desenhar);

    function desenhar(e) {
        if (!desenhando) return;
        const rect = canvas.getBoundingClientRect();
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#000';
        ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
    }

    function limparCanvas() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.beginPath();
    }

    // Enviar assinatura como Base64
    document.querySelector('form').addEventListener('submit', function(e){
        document.getElementById('assinaturaBase64').value = canvas.toDataURL();
    });
</script>
@endsection
