<?php
 
$arqPerguntas = 'perguntas.txt';
 
$mensagem = "";
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$pergunta = "";
$gabarito = "";
$achou = false;
 
//Busca a pergunta pelo ID
if(file_exists($arqPerguntas)){
    $linhas = file($arqPerguntas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach($linhas as $linha){
        $cols = explode(';', $linha);
        if((int)$cols[0] === $id){
            $achou = true;
            $pergunta = $cols[1];
            $gabarito = $cols[3] ?? '';
        }
    }
}

if($achou && $_SERVER['REQUEST_METHOD'] === 'POST'){
    $pergunta = trim($_POST['pergunta']);
    $gabarito = trim($_POST['gabarito']);

    // Reescreve perguntas.txt trocando só a linha desse ID
    $novas = [];
    foreach($linha as $linha){
        $cols = explode(';', $linha);
        if((int)$cols[0] === $id){
            $linha = "{$id};{$pergunta};resposta;{$gabarito}";
        }
        $novas[] = $linha;
    }
    file_put_contents($arqPerguntas, implode("\n", $novas) . "\n", LOCK_EX);
    $mensagem = "A pergunta foi alterada.";
}
?>