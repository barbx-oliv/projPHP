<?php
session_start();
require_once 'BD/BD.php';

$ordem  = $_GET['ordem'] ?? 'recente';
$genero = $_GET['genero'] ?? '';

// CORRIGIDO: Apontado para a tabela real 'cds', removido 'tipo = cd' e ajustado para o booleano do PostgreSQL
$sql = "SELECT * FROM cds WHERE ativo = TRUE AND vendido = FALSE";

if ($genero) {
    $sql .= " AND genero = " . $pdo->quote($genero);
}

$sql .= match($ordem) {
    'preco_asc'  => " ORDER BY preco ASC",
    'preco_desc' => " ORDER BY preco DESC",
    default      => " ORDER BY created_at DESC",
};

// CORRIGIDO: Adicionado FETCH_ASSOC para garantir a leitura correta das colunas pelo PHP
$produtos = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// CORRIGIDO: Puxando a lista de filtros de gêneros diretamente da tabela real de 'cds'
$generos  = $pdo->query("SELECT DISTINCT genero FROM cds WHERE genero IS NOT NULL ORDER BY genero")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetroMusic — CDs</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <?php include "header.php"; ?>

    <div class="pagina-listagem">
        <div class="listagem-header">
            <h1>CD's</h1>
            <div class="filtros">
                <select class="filtro-select" onchange="aplicarFiltro('genero', this.value)">
                    <option value="">Todos os gêneros</option>
                    <?php foreach ($generos as $g): ?>
                        <option value="<?= htmlspecialchars($g) ?>" <?= $genero === $g ? 'selected' : '' ?>>
                            <?= htmlspecialchars($g) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select class="filtro-select" onchange="aplicarFiltro('ordem', this.value)">
                    <option value="recente" <?= $ordem === 'recente' ? 'selected' : '' ?>>Mais recentes</option>
                    <option value="preco_asc" <?= $ordem === 'preco_asc' ? 'selected' : '' ?>>Menor preço</option>
                    <option value="preco_desc" <?= $ordem === 'preco_desc' ? 'selected' : '' ?>>Maior preço</option>
                </select>
            </div>
        </div>

        <div class="produtos-grid">
            <?php if (empty($produtos)): ?>
                <p style="color: var(--ferrugem); padding: 1rem 0;">Nenhum CD encontrado.</p>
            <?php else: ?>
                <?php foreach ($produtos as $p):
                    $preco_final = $p['desconto_percent'] > 0
                        ? $p['preco'] * (1 - $p['desconto_percent'] / 100)
                        : $p['preco'];
                ?>
                <article class="produto">
                    <img src="<?= htmlspecialchars($p['imagem'] ?: 'img/placeholder.jpg') ?>" alt="<?= htmlspecialchars($p['nome']) ?>">
                    <div class="info_produto">
                        <h3><?= htmlspecialchars($p['nome']) ?></h3>
                        <span class="estadoCapa">Capa: <?= htmlspecialchars($p['estado_capa']) ?></span>
                        
                        <span class="estadoDisco">CD: <?= htmlspecialchars($p['estado_disco']) ?></span>
                        
                        <?php if ($p['desconto_percent'] > 0): ?>
                            <p class="precoA">R$ <?= number_format($p['preco'], 2, ',', '.') ?></p>
                        <?php endif; ?>
                        <div class="preco_container">
                            <span class="preco">R$ <?= number_format($preco_final, 2, ',', '.') ?></span>
                            <?php if ($p['desconto_percent'] > 0): ?>
                                <span class="desconto"><?= $p['desconto_percent'] ?>% OFF</span>
                            <?php endif; ?>
                        </div>
                        <p class="parcela">12x de R$ <?= number_format($preco_final / 12, 2, ',', '.') ?></p>
                        <a href="produto.php?id=<?= $p['id'] ?>" class="btn-comprar">Ver produto</a>
                    </div>
                </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php include "footer.php"; ?>

    <script>
    function aplicarFiltro(chave, valor) {
        const params = new URLSearchParams(window.location.search);
        params.set(chave, valor);
        window.location.search = params.toString();
    }
    </script>
</body>
</html>