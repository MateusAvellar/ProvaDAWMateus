<?php

$arqPerguntas = 'perguntas.txt';
$arqRespostas = 'respostas.txt';

$mensagem = "";

//request método post
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $perguntaTexto = trim($_POST['pergunta']);
    $alternativas = $_POST['alternativas'];
    $corretaIndex = (int)$_POST['correta'];

    //Pegar novo ID
    $novoId = 1;
    //se o arquivo existir 
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
    $linhaPergunta = "{$novoId};{$perguntaTexto};resposta;gabarito\n";
    file_put_contents($arqPerguntas, $linhaPergunta, FILE_APPEND | LOCK_EX);

    // Salva em respostas.txt
    //ID_pergunta;ID_resposta;resposta;eh_correta
    $idResposta = 1;
    foreach($alternativas as $index => $textoAlt){
        $textoAlt = trim($textoAlt);
        if($textoAlt !== ''){
            $ehCorreta = ($index === $corretaIndex) ? 1 : 0;
            $linhaResposta = "{$novoId};{$idResposta};{$textoAlt};{$ehCorreta}\n";
            file_put_contents($arqRespostas, $linhaResposta, FILE_APPEND | LOCK_EX);
            $idResposta++;
        }
    }
    $mensagem = "A pergunta foi criada.";
}
?>

