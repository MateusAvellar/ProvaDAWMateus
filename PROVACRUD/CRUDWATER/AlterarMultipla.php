<?php

$arqPerguntas = 'perguntas.txt';
$arqRespostas = 'respostas.txt';

$mensagem = "";
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$pergunta = "";
$alternativas = [];
$corretaIndex = -1;
$achou = false;

//Busca a pergunta pelo ID
if(file_exists($arqPerguntas)){
    $linhas = file($arqPerguntas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach($linhas as $linha){
        $cols = explode(';', $linha);
        if((int)$cols[0] === $id){
            $achou = true;
            $pergunta = $cols[1];
        }
    }
}

if($achou && $_SERVER['REQUEST_METHOD'] === 'POST'){
    $pergunta = trim($_POST['pergunta']);
    $alternativas = $_POST['alternativas'];
    $corretaIndex = (int)$_POST['correta'];

    // Reescreve perguntas.txt trocando só a linha desse ID
    $novas = [];
    foreach($linha as $linhas){
        $cols = explode(';', $linha);
        if((int)$cols[0] === $id){
            $linha = "{$id};{$pergunta};resposta;gabarito";
        }
        $novas[] = $linha;
    }
    file_put_contents($arqPerguntas, implode("\n", $novas) . "\n", LOCK_EX);

    // Reescreve respostas.txt: mantém as outras perguntas e regrava as desse ID
    $outras = [];
    if(file_exists($arqRespostas)){
        $linhasResp = file($arqRespostas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach($linhasResp as $linha){
            $cols = explode(';', $linha);
            if((int)$cols[0] !== $id){
                $outras[] = $linha;
            }
        }
    }
    $idResposta = 1;
    foreach($alternativas as $index => $textoAlt){
        $textoAlt = trim($textoAlt);
        if($textoAlt !== ''){
            $ehCorreta = ($index === $corretaIndex) ? 1 : 0;
            $outras[] = "{$id};{$idResposta};{$textoAlt};{$ehCorreta}";
            $idResposta++;
        }
    }
    file_put_contents($arqRespostas, $outras ? implode("\n", $outras) . "\n" : "", LOCK_EX);
    $mensagem = "A pergunta foi alterada.";

} elseif($achou && file_exists($arqRespostas)){
    // Carrega as alternativas atuais para preencher a tela
    $linhasResp = file($arqRespostas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach($linhasResp as $linha){
        $cols = explode(';', $linha);
        if((int)$cols[0] === $id){
            $alternativas[] = $cols[2];
            if((int)$cols[3] === 1){
                $corretaIndex = count($alternativas) - 1;
            }
        }
    }
}
?>