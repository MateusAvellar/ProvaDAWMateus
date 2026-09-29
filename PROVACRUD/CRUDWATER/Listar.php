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
<!DOCTYPE html>
<html lang="pt-br">
<head><meta charset="utf-8"><title>Perguntas</title></head>
<body>
<h2>Perguntas e respostas</h2>
<p><a href="index.php">Início</a></p>
<?php if(!$perguntas): ?>
    <p>Nenhuma pergunta cadastrada.</p>
<?php else: ?>
<table border="1" cellpadding="6" cellspacing="0">
    <tr><th>ID</th><th>Pergunta</th><th>Tipo</th><th>Respostas</th><th>Ações</th></tr>
    <?php foreach($perguntas as $p):
        $pid = (int)$p[0];
        $multipla = isset($respostas[$pid]);
        $tela = $multipla ? 'multipla' : 'texto';
    ?>
    <tr>
        <td><?= $pid ?></td>
        <td><?= htmlspecialchars($p[1]) ?></td>
        <td><?= $multipla ? 'Múltipla escolha' : 'Texto' ?></td>
        <td>
            <?php if($multipla): foreach($respostas[$pid] as $r): ?>
                <?= ((int)$r[3] === 1 ? '&#10004; ' : '&#10008; ') . htmlspecialchars($r[2]) ?><br>
            <?php endforeach; else: ?>
                <?= htmlspecialchars($p[3] ?? '') ?>
            <?php endif; ?>
        </td>
        <td>
            <a href="pergunta_ver.php?id=<?= $pid ?>">Ver</a> |
            <a href="pergunta_alterar_<?= $tela ?>.php?id=<?= $pid ?>">Alterar</a> |
            <a href="pergunta_excluir.php?id=<?= $pid ?>">Excluir</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>