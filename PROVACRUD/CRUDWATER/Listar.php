<?php
 
// Arquivos onde perguntas e respostas são armazenadas
$arqPerguntas = 'perguntas.txt';
$arqRespostas = 'respostas.txt';
 
// Lista de perguntas (cada item é um array com as colunas da linha)
$perguntas = [];
$respostas = [];   // agrupadas por ID da pergunta
 
// Carrega todas as perguntas do arquivo
if(file_exists($arqPerguntas)){
    $linhas = file($arqPerguntas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach($linhas as $linha){
        $perguntas[] = explode(';', $linha);
    }
}
// Carrega as alternativas e as agrupa pelo ID da pergunta (coluna 0)
if(file_exists($arqRespostas)){
    $linhas = file($arqRespostas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach($linhas as $linha){
        $cols = explode(';', $linha);
        $respostas[(int)$cols[0]][] = $cols;
    }
}
?>