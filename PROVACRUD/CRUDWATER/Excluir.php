<?php
 
// Arquivos onde perguntas e respostas são armazenadas
$arqPerguntas = 'perguntas.txt';
$arqRespostas = 'respostas.txt';
 
// Mensagem de feedback para o usuário
$mensagem = "";
// ID da pergunta via GET ou POST (0 se não informado)
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
// Guardará os campos da pergunta encontrada (null = não encontrada)
$pergunta = null;
 
// Busca a pergunta pelo ID
if(file_exists($arqPerguntas)){
    $linhas = file($arqPerguntas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach($linhas as $linha){
        $cols = explode(';', $linha);
        if((int)$cols[0] === $id){
            $pergunta = $cols;
        }
    }
}
 
// Só exclui se a pergunta existe e a confirmação foi enviada (POST)
if($pergunta && $_SERVER['REQUEST_METHOD'] === 'POST'){
    // Remove de perguntas.txt
    // Mantém todas as linhas, exceto a do ID excluído
    $mantem = [];
    foreach($linha as $linha){
        $cols = explode(';', $linha);
        if((int)$cols[0] !== $id){
            $mantem[] = $linha;
        }
    }
    // Regrava o arquivo (vazio se não sobrou nenhuma linha)
    file_put_contents($arqPerguntas, $mantem ? implode("\n", $mantem) . "\n" : "", LOCK_EX);
 
    // Remove de respostas.txt
    // Apaga todas as alternativas ligadas a essa pergunta
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
    // Limpa a variável para indicar que a pergunta não existe mais
    $pergunta = null;
}
?>