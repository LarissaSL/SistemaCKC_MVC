// Função para recalcular a pontuação quando houver uma mudança na posição do piloto
function recalculaPontuacao() {
    var pilotos = document.querySelectorAll('.pilot');
    pilotos.forEach(function(pilotoDiv) {
        gerarPontuacao(pilotoDiv);
    });
}

document.addEventListener('DOMContentLoaded', function () {
    // Inicializa a primeira vez se necessário
    var numeroPilotos = pilotosContainer.querySelectorAll('.pilot').length;
    atualizarNumeroPilotos(numeroPilotos);

    // Chama a função gerarPontuacao para cada piloto presente na página
    var pilotos = document.querySelectorAll('.pilot');
    pilotos.forEach(function(pilotoDiv) {
        gerarPontuacao(pilotoDiv);
    });

    // Adiciona evento ao botão de adicionar piloto
    document.getElementById("addPilot").addEventListener("click", function () {
        adicionarPiloto();
    });

    // Delegação de eventos para o botão de excluir registro
    document.getElementById("pilotosContainer").addEventListener("click", function (event) {
        if (event.target && event.target.className == "btn_excluirRegistro") {
            var pilotoDiv = event.target.closest(".pilot");
            var numeroPilotos = pilotosContainer.querySelectorAll('.pilot').length;
            var hiddenID = pilotoDiv.querySelector("input[name='ids[]']");
            if (hiddenID && hiddenID.value !== "") {
                var btn = event.target;
                
                // Confirma a exclusão antes de prosseguir
                if(confirmarExclusao(btn)){
                    pilotoDiv.remove();
                    atualizarNumeroPilotos(numeroPilotos - 1);
                    recalcularPosicoesEPontuacoes();
                }
            } else {
                // Se não houver campo hidden ou o campo estiver vazio, apenas remova a div do piloto
                pilotoDiv.remove();
                atualizarNumeroPilotos(numeroPilotos - 1);
                recalcularPosicoesEPontuacoes();
            }
        }
    });

    // Adiciona evento de mudança ao select de posição para recalcular a pontuação
    document.getElementById("pilotosContainer").addEventListener("change", function (event) {
        if (event.target && event.target.name == "posicoes[]") {
            recalculaPontuacao();
        }
    });
});

// Função para popular o select de posições
function popularPosicoesSelect(selectElement, posicaoInicial) {
    var posicoes = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15"];
    posicoes.forEach(function (posicao) {
        var option = document.createElement("option");
        option.value = posicao;
        option.textContent = posicao + " º";
        selectElement.appendChild(option);
    });

    // Define a posição inicial do select
    selectElement.value = posicaoInicial;

    // Adiciona evento de mudança ao select
    selectElement.addEventListener('change', function () {
        var pilotoDiv = selectElement.parentElement;
        gerarPontuacao(pilotoDiv);
    });
}

// Função para popular o select de pilotos
function popularPilotosSelect(selectElement, usuarios) {
    usuarios.forEach(function (usuario) {
        var option = document.createElement("option");
        option.value = usuario.id;
        option.textContent = usuario.nome + ' ' + usuario.sobrenome;
        selectElement.appendChild(option);
    });
}

function adicionarPiloto() {
    var formPilotos = document.getElementById('formPilotos'); 
    var pilotosContainer = formPilotos.querySelector('#pilotosContainer'); 
    var pilotos = pilotosContainer.querySelectorAll('.pilot');
    var numeroPilotos = pilotosContainer.querySelectorAll('.pilot').length;
    var maiorPosicao = 0;

    // Encontra a maior posição já selecionada
    pilotos.forEach(function (pilotoDiv) {
        var posicao = parseInt(pilotoDiv.querySelector("select[name='posicoes[]']").value);
        if (posicao > maiorPosicao) {
            maiorPosicao = posicao;
        }
    });

    // Incrementa a posição para a próxima disponível
    var proximaPosicao = (maiorPosicao + 1).toString();

    var pilotoDiv = document.createElement('div');
    pilotoDiv.className = 'pilot';

    var hiddenID = document.createElement('input');
    hiddenID.type = 'hidden';
    hiddenID.name = 'ids[]';
    hiddenID.value = ''; 
    pilotoDiv.appendChild(hiddenID);

    var divCampos1 = document.createElement('div');
    divCampos1.className = 'campos';
    pilotoDiv.appendChild(divCampos1);

    var labelPosicao = document.createElement('label');
    labelPosicao.textContent = 'Posição:';
    divCampos1.appendChild(labelPosicao);

    var selectPosicao = document.createElement('select');
    selectPosicao.id = 'posicao';
    selectPosicao.className = 'selecao';
    selectPosicao.name = 'posicoes[]';
    selectPosicao.required = true;
    popularPosicoesSelect(selectPosicao, proximaPosicao);
    divCampos1.appendChild(selectPosicao);

    // Adiciona evento de mudança ao select de posição
    selectPosicao.addEventListener('change', function () {
        gerarPontuacao(pilotoDiv);
    });

    var divCampos2 = document.createElement('div');
    divCampos2.className = 'campos';
    pilotoDiv.appendChild(divCampos2);

    var labelPiloto = document.createElement('label');
    labelPiloto.textContent = 'Piloto:';
    divCampos2.appendChild(labelPiloto);

    var selectPiloto = document.createElement('select');
    selectPiloto.name = 'pilotos[]';
    selectPiloto.required = true;
    selectPiloto.className = 'selecao';
    popularPilotosSelect(selectPiloto, usuarios);
    divCampos2.appendChild(selectPiloto);

    var divCampos3 = document.createElement('div');
    divCampos3.className = 'campos';
    pilotoDiv.appendChild(divCampos3);

    var labelMelhorTempo = document.createElement('label');
    labelMelhorTempo.textContent = 'Melhor tempo:';
    divCampos3.appendChild(labelMelhorTempo);

    var inputMelhorTempo = document.createElement('input');
    inputMelhorTempo.type = 'time';
    inputMelhorTempo.name = 'melhor_tempo[]';
    inputMelhorTempo.placeholder = 'Melhor Tempo';
    inputMelhorTempo.required = true;
    divCampos3.appendChild(inputMelhorTempo);

    var divCampos4 = document.createElement('div');
    divCampos4.className = 'campos';
    pilotoDiv.appendChild(divCampos4);

    var labelPontuacao = document.createElement('label');
    labelPontuacao.textContent = 'Pontuação:';
    divCampos4.appendChild(labelPontuacao);

    var inputPontuacao = document.createElement('input');
    inputPontuacao.type = 'number';
    inputPontuacao.name = 'pontuacao[]';
    inputPontuacao.readOnly = true;
    divCampos4.appendChild(inputPontuacao);

    var divCampos5 = document.createElement('div');
    divCampos5.className = 'campos';
    pilotoDiv.appendChild(divCampos5);

    var btnExcluirRegistro = document.createElement('button');
    btnExcluirRegistro.type = 'button';
    btnExcluirRegistro.className = 'btn_excluirRegistro';
    btnExcluirRegistro.textContent = 'Excluir registro';
    divCampos5.appendChild(btnExcluirRegistro);

    pilotosContainer.appendChild(pilotoDiv);

    // Gerar a pontuação inicial
    gerarPontuacao(pilotoDiv);

    // Atualiza o número de pilotos com resultados
    atualizarNumeroPilotos(numeroPilotos + 1);
}

// Função para atualizar o número de pilotos com resultados
function atualizarNumeroPilotos(numero) {
    var paragrafoQtdPilotos = document.getElementById('qtdPilotos');
    paragrafoQtdPilotos.textContent = "Qtd. de Pilotos nesse resultado: " + numero + "/15";
}

// Função para gerar a pontuação baseada na posição
function gerarPontuacao(pilotoDiv) {
    var posicao = parseInt(pilotoDiv.querySelector("select[name='posicoes[]']").value);
    var pontuacaoPosicao;
    switch (posicao) {
        case 1:
            pontuacaoPosicao = 20;
            break;
        case 2:
            pontuacaoPosicao = 18;
            break;
        case 3:
            pontuacaoPosicao = 16;
            break;
        case 4:
            pontuacaoPosicao = 14;
            break;
        case 5:
            pontuacaoPosicao = 12;
            break;
        case 6:
            pontuacaoPosicao = 10;
            break;
        case 7:
            pontuacaoPosicao = 9;
            break;
        case 8:
            pontuacaoPosicao = 8;
            break;
        case 9:
            pontuacaoPosicao = 7;
            break;
        case 10:
            pontuacaoPosicao = 6;
            break;
        case 11:
            pontuacaoPosicao = 5;
            break;
        case 12:
            pontuacaoPosicao = 4;
            break;
        case 13:
            pontuacaoPosicao = 3;
            break;
        case 14:
            pontuacaoPosicao = 2;
            break;
        case 15:
            pontuacaoPosicao = 1;
            break;
        default:
            pontuacaoPosicao = 0; // Caso a posição não esteja no intervalo de 1 a 15
    }

    pilotoDiv.querySelector("input[name='pontuacao[]']").value = pontuacaoPosicao;

    var pilotoSelect = pilotoDiv.querySelector("select[name='pilotos[]']");
    var nomePiloto = pilotoSelect.options[pilotoSelect.selectedIndex].text;

    console.log("Pontuação calculada para " + nomePiloto + ": " + pontuacaoPosicao);
}

// Função para recalcular as posições e pontuações quando um registro for excluído
function recalcularPosicoesEPontuacoes() {
    var pilotos = document.querySelectorAll('.pilot');
    pilotos.forEach(function(pilotoDiv, index) {
        var novaPosicao = (index + 1).toString();
        var selectPosicao = pilotoDiv.querySelector("select[name='posicoes[]']");
        selectPosicao.value = novaPosicao;
        gerarPontuacao(pilotoDiv);
    });
}

function confirmarExclusao(btn) {
    console.log("Função confirmarExclusao foi chamada.");
    var pilotoDiv = btn.closest('.pilot');
    var resultadoID = pilotoDiv.querySelector("input[name='ids[]']").value;
    var posicao = pilotoDiv.querySelector(".selecao[name='posicoes[]']").value;
    var pontuacao = pilotoDiv.querySelector("input[name='pontuacao[]']").value;
    var nomePiloto = pilotoDiv.querySelector(".selecao[name='pilotos[]'] option:checked").innerText.trim();

    // Alerta de confirmação
    var confirmacao = confirm(`Tem certeza que deseja EXCLUIR esse resultado:\n\n${posicao}º   |   ${nomePiloto}   |   ${pontuacao} pts\n\nOBS.: TODAS as posições e pontuações serão recalculadas\nOBS.2: Essa ação é irreversível!`);

    // Se o usuário confirmar a exclusão
    if (confirmacao) {
        excluirResultado(resultadoID);
        return true;
    } else {
        console.log("Exclusão cancelada.");
        return false;
    }
}

// Requisição AJAX
function excluirResultado(resultadoID) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/sistemackc/admtm85/resultado/excluir/resultado/" + resultadoID, true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4) {
            if (xhr.status == 200) {
                console.log(xhr.responseText);
                // Recarrega a página após a exclusão bem-sucedida
                location.reload();
            } else {
                console.error('Erro ao excluir resultado. Status: ' + xhr.status);
            }
        }
    };
    xhr.send();
}