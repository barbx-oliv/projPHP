<?php
session_start();
require_once 'BD/BD.php';

// Redireciona se não estiver logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Dados do usuário
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch();

// CORRIGIDO: Substituída a tabela 'produtos' por um UNION das tabelas reais (discos e cds)
$query_produtos = "
    SELECT id, nome, preco, ativo, vendido, 'vinil' AS tipo, created_at 
    FROM discos 
    WHERE usuario_id = ?
    
    UNION ALL
    
    SELECT id, nome, preco, ativo, vendido, 'cd' AS tipo, created_at 
    FROM cds 
    WHERE usuario_id = ?
    
    ORDER BY created_at DESC
";

$meus_produtos = $pdo->prepare($query_produtos);
// Passamos o ID duas vezes porque temos dois parâmetros '?' na query unificada
$meus_produtos->execute([$usuario_id, $usuario_id]);
$produtos = $meus_produtos->fetchAll(PDO::FETCH_ASSOC);

// Contagem (funciona perfeitamente agora que a lista traz dados reais)
$total_produtos = count($produtos);
$total_vendidos = array_reduce($produtos, fn($c, $p) => $c + ($p['vendido'] ? 1 : 0), 0);

// Aba ativa
$aba = $_GET['aba'] ?? 'meus-produtos';

// Atualizar perfil
$sucesso = '';
$erro    = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    if ($_POST['acao'] === 'atualizar_perfil') {
        $nome = trim($_POST['nome'] ?? '');
        if (empty($nome)) {
            $erro = 'O nome não pode ficar vazio.';
        } else {
            $pdo->prepare("UPDATE usuarios SET nome = ? WHERE id = ?")
                ->execute([$nome, $usuario_id]);
            $_SESSION['usuario_nome'] = $nome;
            $usuario['nome'] = $nome;
            $sucesso = 'Perfil updated!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetroMusic — Perfil</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <?php include "header.php"; ?>

    <div class="pagina-perfil">