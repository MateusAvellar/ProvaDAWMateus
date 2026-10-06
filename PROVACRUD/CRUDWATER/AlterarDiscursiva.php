<?php
 
// Caminho do arquivo onde as perguntas ficam armazenadas
$arqPerguntas = 'perguntas.txt';
 
// Mensagem de feedback
$mensagem = "";

// Obtém o ID da pergunta via POST (formulário); se nenhum existir, assume 0.
// O cast (int) garante que o valor seja sempre um número inteiro
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

// Variáveis que guardarão os dados da pergunta encontrada
$pergunta = "";
$gabarito = "";

// indica se achou a pergunta
$achou = false;
 
//Busca a pergunta pelo ID
// Só tenta ler o arquivo se ele existir, evitando erros
if(file_exists($arqPerguntas)){
    // Lê o arquivo em um array, uma posição por linha,
    // removendo as quebras de linha e ignorando linhas vazias
    $linhas = file($arqPerguntas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    // Percorre cada linha do arquivo
    foreach($linhas as $linha){
        // Separa os campos da linha usando ponto e vírgula como delimitador
        $cols = explode(';', $linha);
        // Compara o ID da linha (coluna 0) com o ID procurado
        if((int)$cols[0] === $id){
            $achou = true;
            // Coluna 1 contém o texto da pergunta
            $pergunta = $cols[1];
            // Coluna 3 contém o gabarito; se não existir, usa string vazia
            $gabarito = $cols[3] ?? '';
        }
    }
}

// Só processa a alteração se a pergunta existe e o formulário foi enviado (método POST)
if($achou && $_SERVER['REQUEST_METHOD'] === 'POST'){
    // Recebe os novos valores do formulário, removendo espaços extras nas pontas
    $pergunta = trim($_POST['pergunta']);
    $gabarito = trim($_POST['gabarito']);

    // Reescreve perguntas.txt trocando só a linha desse ID
    // Array que armazenará todas as linhas do arquivo (já com a linha alterada)
    $novas = [];
    foreach($linha as $linha){
        // Separa os campos da linha atual
        $cols = explode(';', $linha);
        // Se for a linha do ID editado, monta a nova linha no formato
        // id;pergunta;resposta;gabarito
        if((int)$cols[0] === $id){
            $linha = "{$id};{$pergunta};resposta;{$gabarito}";
        }
        // Adiciona a linha ao novo conjunto
        $novas[] = $linha;
    }
    // Grava o arquivo inteiro novamente, unindo as linhas com quebra de linha
    // e adicionando uma quebra ao final. LOCK_EX bloqueia o arquivo durante a
    // escrita para evitar conflitos de acesso simultâneo
    file_put_contents($arqPerguntas, implode("\n", $novas) . "\n", LOCK_EX);

    // Define a mensagem de sucesso para o usuário
    $mensagem = "A pergunta foi alterada.";
}
?>