<?php
// CRUD de usuários - usuarios.txt: ID;nome;email;senha_hash
 
// Arquivo onde os usuários são armazenados
$arqUsuarios = 'usuarios.txt';
 
// Mensagem de feedback (erros de validação)
$mensagem = "";
// Ação desejada, via URL: listar (padrão), novo, editar ou excluir
$acao = $_GET['acao'] ?? 'listar';   // listar, novo, editar, excluir
// ID do usuário via GET ou POST (0 se não informado)
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
 
//Carrega todos os usuários
// Cada usuário vira um array com as colunas da linha
$usuarios = [];
if(file_exists($arqUsuarios)){
    $linhas = file($arqUsuarios, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach($linhas as $linha){
        $usuarios[] = explode(';', $linha);
    }
}
 
//Salva o array de volta no arquivo
// Monta uma linha por usuário e regrava o arquivo inteiro
function salvarUsuarios($arq, $usuarios){
    $txt = "";
    foreach($usuarios as $u){
        $txt .= implode(';', $u) . "\n";
    }
    file_put_contents($arq, $txt, LOCK_EX);
}
 
//Procura o usuário atual
// Fica null se o ID não existir
$atual = null;
foreach($usuarios as $u){
    if((int)$u[0] === $id){
        $atual = $u;
    }
}
 
// Só processa alterações quando o formulário foi enviado
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // Criação ou edição
    if($acao === 'novo' || $acao === 'editar'){
        // Troca ";" por "," para não quebrar o formato do arquivo
        $nome = str_replace(';', ',', trim($_POST['nome']));
        $email = str_replace(';', ',', trim($_POST['email']));
        $senha = $_POST['senha'];
 
        // Validação: nome e e-mail são obrigatórios
        if($nome === '' || $email === ''){
            $mensagem = "Informe nome e e-mail.";
        } elseif($acao === 'novo'){
            // Novo ID = maior ID existente + 1
            $novoId = 1;
            foreach($usuarios as $u){
                if((int)$u[0] >= $novoId){
                    $novoId = (int)$u[0] + 1;
                }
            }
            // Adiciona o usuário, com a senha armazenada como hash
            $usuarios[] = [$novoId, $nome, $email, password_hash($senha, PASSWORD_DEFAULT)];
            salvarUsuarios($arqUsuarios, $usuarios);
            // Redireciona para a listagem
            header('Location: usuarios.php'); exit;
        } else {
            // Edição: atualiza os dados do usuário com o ID informado
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
    // Exclusão: só se o usuário existir
    } elseif($acao === 'excluir' && $atual){
        // Mantém todos os usuários, exceto o do ID excluído, e reindexa o array
        $usuarios = array_values(array_filter($usuarios, fn($u) => (int)$u[0] !== $id));
        salvarUsuarios($arqUsuarios, $usuarios);
        header('Location: usuarios.php'); exit;
    }
}
?>