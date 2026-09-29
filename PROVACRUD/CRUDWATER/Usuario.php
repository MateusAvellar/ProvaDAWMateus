<?php
// CRUD de usuários - usuarios.txt: ID;nome;email;senha_hash
 
$arqUsuarios = 'usuarios.txt';
 
$mensagem = "";
$acao = $_GET['acao'] ?? 'listar';   // listar, novo, editar, excluir
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
 
//Carrega todos os usuários
$usuarios = [];
if(file_exists($arqUsuarios)){
    $linhas = file($arqUsuarios, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach($linhas as $linha){
        $usuarios[] = explode(';', $linha);
    }
}
 
//Salva o array de volta no arquivo
function salvarUsuarios($arq, $usuarios){
    $txt = "";
    foreach($usuarios as $u){
        $txt .= implode(';', $u) . "\n";
    }
    file_put_contents($arq, $txt, LOCK_EX);
}
 
//Procura o usuário atual
$atual = null;
foreach($usuarios as $u){
    if((int)$u[0] === $id){
        $atual = $u;
    }
}
 
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if($acao === 'novo' || $acao === 'editar'){
        $nome = str_replace(';', ',', trim($_POST['nome']));
        $email = str_replace(';', ',', trim($_POST['email']));
        $senha = $_POST['senha'];
 
        if($nome === '' || $email === ''){
            $mensagem = "Informe nome e e-mail.";
        } elseif($acao === 'novo'){
            $novoId = 1;
            foreach($usuarios as $u){
                if((int)$u[0] >= $novoId){
                    $novoId = (int)$u[0] + 1;
                }
            }
            $usuarios[] = [$novoId, $nome, $email, password_hash($senha, PASSWORD_DEFAULT)];
            salvarUsuarios($arqUsuarios, $usuarios);
            header('Location: usuarios.php'); exit;
        } else {
            foreach($usuarios as $i => $u){
                if((int)$u[0] === $id){
                    $usuarios[$i][1] = $nome;
                    $usuarios[$i][2] = $email;
                    if($senha !== ''){   // vazio = mantém a senha atual
                        $usuarios[$i][3] = password_hash($senha, PASSWORD_DEFAULT);
                    }
                }
            }
            salvarUsuarios($arqUsuarios, $usuarios);
            header('Location: usuarios.php'); exit;
        }
    } elseif($acao === 'excluir' && $atual){
        $usuarios = array_values(array_filter($usuarios, fn($u) => (int)$u[0] !== $id));
        salvarUsuarios($arqUsuarios, $usuarios);
        header('Location: usuarios.php'); exit;
    }
}
?>