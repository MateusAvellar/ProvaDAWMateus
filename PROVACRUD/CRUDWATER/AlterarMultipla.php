<?php

// Caminhos dos arquivos de texto usados como "banco de dados"
$arqPerguntas = 'perguntas.txt';
$arqRespostas = 'respostas.txt';

// Mensagem de feedback 
$mensagem = "";

// Obtém o ID da pergunta via GET (URL) ou POST (formulário); se nenhum existir, assume 0.
// O cast (int) garante que o valor seja sempre um número inteiro
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
// Texto da pergunta que será exibida/editada
$pergunta = "";
// Lista com o texto de cada alternativa da pergunta
$alternativas = [];
// Posição (índice) da alternativa correta dentro do array; -1 significa "nenhuma definida"
$corretaIndex = -1;
// Flag que indica se a pergunta com o ID informado foi localizada
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
        }
    }
}

// Só processa a alteração se a pergunta existe e o formulário foi enviado (método POST)
if($achou && $_SERVER['REQUEST_METHOD'] === 'POST'){
    // Recebe os novos valores do formulário
    // trim() remove espaços extras no início e no fim do texto da pergunta
    $pergunta = trim($_POST['pergunta']);
    // Array com os textos das alternativas enviadas pelo formulário
    $alternativas = $_POST['alternativas'];
    // Índice da alternativa marcada como correta (convertido para inteiro)
    $corretaIndex = (int)$_POST['correta'];

    // Reescreve perguntas.txt trocando só a linha desse ID
    // Array que armazenará todas as linhas do arquivo (já com a linha alterada)
    $novas = [];
    foreach($linha as $linhas){
        // Separa os campos da linha atual
        $cols = explode(';', $linha);
        // Se for a linha do ID editado, monta a nova linha no formato
        // id;pergunta;resposta;gabarito
        if((int)$cols[0] === $id){
            $linha = "{$id};{$pergunta};resposta;gabarito";
        }
        // Adiciona a linha (alterada ou não) ao novo conjunto
        $novas[] = $linha;
    }
    // Grava o arquivo inteiro novamente, unindo as linhas com quebra de linha
    // e adicionando uma quebra ao final. LOCK_EX bloqueia o arquivo durante a
    // escrita para evitar conflitos de acesso simultâneo
    file_put_contents($arqPerguntas, implode("\n", $novas) . "\n", LOCK_EX);

    // Reescreve respostas.txt: mantém as outras perguntas e regrava as desse ID
    // Array que guardará as linhas de respostas das OUTRAS perguntas (que não serão alteradas)
    $outras = [];
    // Só lê o arquivo de respostas se ele existir
    if(file_exists($arqRespostas)){
        // Lê todas as linhas do arquivo de respostas, ignorando quebras e linhas vazias
        $linhasResp = file($arqRespostas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach($linhasResp as $linha){
            $cols = explode(';', $linha);
            // Mantém apenas as linhas cujo ID seja diferente do editado;
            // as alternativas antigas desta pergunta são descartadas
            if((int)$cols[0] !== $id){
                $outras[] = $linha;
            }
        }
    }
    // Contador que gera o número sequencial de cada alternativa dentro da pergunta
    $idResposta = 1;
    // Percorre as novas alternativas enviadas pelo formulário
    foreach($alternativas as $index => $textoAlt){
        // Remove espaços extras do texto da alternativa
        $textoAlt = trim($textoAlt);
        // Ignora alternativas deixadas em branco
        if($textoAlt !== ''){
            // Define 1 se o índice for o da alternativa correta, ou 0 caso contrário
            $ehCorreta = ($index === $corretaIndex) ? 1 : 0;
            // Monta a linha no formato: idPergunta;idResposta;texto;ehCorreta
            $outras[] = "{$id};{$idResposta};{$textoAlt};{$ehCorreta}";
            // Incrementa o contador para a próxima alternativa
            $idResposta++;
        }
    }
    // Grava o arquivo de respostas. Se não houver nenhuma linha, grava vazio;
    // caso contrário, une as linhas com quebra de linha e adiciona uma ao final
    file_put_contents($arqRespostas, $outras ? implode("\n", $outras) . "\n" : "", LOCK_EX);
    // Define a mensagem de sucesso para o usuário
    $mensagem = "A pergunta foi alterada.";

// Se não for um POST (apenas exibição da tela) e a pergunta existir, carrega as alternativas
} elseif($achou && file_exists($arqRespostas)){
    // Carrega as alternativas atuais para preencher a tela
    // Lê todas as linhas do arquivo de respostas
    $linhasResp = file($arqRespostas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach($linhasResp as $linha){
        $cols = explode(';', $linha);
        // Considera apenas as alternativas que pertencem à pergunta procurada
        if((int)$cols[0] === $id){
            // Coluna 2 contém o texto da alternativa
            $alternativas[] = $cols[2];
            // Coluna 3 indica se é a correta (1) ou não (0)
            if((int)$cols[3] === 1){
                // Guarda a posição da alternativa correta (último índice adicionado)
                $corretaIndex = count($alternativas) - 1;
            }
        }
    }
}
?>