<?php
 
$arqPerguntas = 'perguntas.txt';
$arqRespostas = 'respostas.txt';
 
$perguntas = [];
$respostas = [];   // agrupadas por ID da pergunta
 
if(file_exists($arqPerguntas)){
    $linhas = file($arqPerguntas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach($linhas as $linha){
        $perguntas[] = explode(';', $linha);
    }
}
if(file_exists($arqRespostas)){
    $linhas = file($arqRespostas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach($linhas as $linha){
        $cols = explode(';', $linha);
        $respostas[(int)$cols[0]][] = $cols;
    }
}
?>
