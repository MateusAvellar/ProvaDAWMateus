<?php
 
$arqPerguntas = 'perguntas.txt';
$arqRespostas = 'respostas.txt';
 
$mensagem = "";
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$pergunta = null;
 
if(file_exists($arqPerguntas)){
    $linhas = file($arqPerguntas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach($linhas as $linha){
        $cols = explode(';', $linha);
        if((int)$cols[0] === $id){
            $pergunta = $cols;
        }
    }
}
 
if($pergunta && $_SERVER['REQUEST_METHOD'] === 'POST'){
    // Remove de perguntas.txt
    $mantem = [];
    foreach($linha as $linha){
        $cols = explode(';', $linha);
        if((int)$cols[0] !== $id){
            $mantem[] = $linha;
        }
    }
    file_put_contents($arqPerguntas, $mantem ? implode("\n", $mantem) . "\n" : "", LOCK_EX);
 
    // Remove de respostas.txt
    if(file_exists($arqRespostas)){
        $linhasResp = file($arqRespostas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $mantem = [];
        foreach($linhasResp as $linha){
            $cols = explode(';', $linha);
            if((int)$cols[0] !== $id){
                $mantem[] = $linha;
            }
        }
        file_put_contents($arqRespostas, $mantem ? implode("\n", $mantem) . "\n" : "", LOCK_EX);
    }
    $mensagem = "A pergunta e suas respostas foram excluídas.";
    $pergunta = null;
}
?>