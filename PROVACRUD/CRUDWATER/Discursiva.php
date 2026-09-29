<?php

$arqPerguntas = 'perguntas.txt';

$mensagem = "";

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $perguntaTexto = trim($_POST['pergunta']);
    $gabaritoTexto = trim($_POST['gabarito']);

    //Novo ID
    $novoId = 1;
    if(file_exists($arqPerguntas)){
        $linhas = file($arqPerguntas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach($linhas as $linha){
            $cols = explode(';', $linha);
            if((int)$cols[0] >= $novoId){
                $novoId = (int)$cols[0] + 1;
            }
        }
    }

    // Salva em perguntas.txt
    //ID;a pergunta;resposta;gabarito
    $linhaPergunta = "{$novoId};{$perguntaTexto};resposta;{$gabaritoTexto}\n";
    file_put_contents($arqPerguntas, $linhaPergunta, FILE_APPEND | LOCK_EX);

    $mensagem = "A pergunta foi criada.";
}
?>

