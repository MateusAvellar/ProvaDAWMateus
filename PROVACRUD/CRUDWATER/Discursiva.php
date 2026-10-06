<?php

// Arquivo onde as perguntas são armazenadas
$arqPerguntas = 'perguntas.txt';

// Mensagem de feedback para o usuário
$mensagem = "";

// Só executa se o formulário foi enviado
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // Recebe os dados do formulário, removendo espaços extras
    $perguntaTexto = trim($_POST['pergunta']);
    $gabaritoTexto = trim($_POST['gabarito']);

    //Novo ID
    // Começa em 1 e, se o arquivo existir, passa a ser o maior ID existente + 1
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
    // Adiciona a nova linha ao final do arquivo (FILE_APPEND), com bloqueio durante a escrita (LOCK_EX)
    $linhaPergunta = "{$novoId};{$perguntaTexto};resposta;{$gabaritoTexto}\n";
    file_put_contents($arqPerguntas, $linhaPergunta, FILE_APPEND | LOCK_EX);

    $mensagem = "A pergunta foi criada.";
}
?>