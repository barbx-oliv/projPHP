<?php
session_start();
require_once 'BD/BD.php';

$ordem = $_GET['ordem'] ?? 'recente';
$genero = $_GET['genero'] ?? '';

// CORRIGIDO: mudado 'ativo = 1' para 'ativo = TRUE' (Padrão correto do Postgres para campos BOOLEAN)
$sql = "SELECT * FROM discos WHERE ativo = TRUE AND vendido = FALSE";

if ($genero) {
    $sql .= " AND genero = " . $pdo->quote($genero);
}

$sql .= match($ordem) {
    'preco_asc'  => " ORDER BY preco ASC",
    'preco_desc' => " ORDER BY preco DESC",
    default      => " ORDER BY created_at DESC",
};

// CORRIGIDO: mudado de $discos para $produtos para bater com o seu HTML lá embaixo
$produtos = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// CORRIGIDO: puxando os gêneros da tabela real 'discos' e removendo o filtro de 'tipo' que não existe nela
$generos  = $pdo->query("SELECT DISTINCT genero FROM discos WHERE genero IS NOT NULL ORDER BY genero")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetroMusic — Discos de Vinil</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <?php include "header.php"; ?>

    <div class="pagina-listagem">
        <div class="listagem-header">
            <h1>Discos de Vinil</h1>
            <div class="filtros">
                <select class="filtro-select" onchange="aplicarFiltro('genero', this.value)">
                    <option value="">Todos os gêneros</option>
                    <?php foreach ($generos as $g): ?>
                        <option value="<?= htmlspecialchars($g) ?>" <?= $genero === $g ? 'selected' : '' ?>>
                            <?= htmlspecialchars($g) ?>
                        </option>
                    <?php endforeach; ?>
                </select