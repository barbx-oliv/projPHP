<?php
session_start();
require_once 'BD/BD.php';

$ordem = $_GET['ordem'] ?? 'recente';
$tipo  = $_GET['tipo'] ?? '';

$sql = "SELECT l.*, u.nome AS vendedor_nome,
               (SELECT COUNT(*) FROM lote_itens WHERE lote_id = l.id) AS qtd_itens
        FROM lotes l
        JOIN usuarios u ON l.usuario_id = u.id
        WHERE l.ativo = 1";

if ($tipo) {
    $sql .= " AND l.tipo_midia = " . $pdo->quote($tipo);
}
$sql .= match($ordem) {
    'preco_asc'  => " ORDER BY l.preco ASC",
    'preco_desc' => " ORDER BY l.preco DESC",
    default      => " ORDER BY l.created_at DESC",
};

$lotes = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetroMusic — Lotes</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <?php include "header.php"; ?>

    <div class="pagina-listagem">
        <div class="listagem-header">
            <h1>Lotes</h1>
            <p style="font-size:.85rem; color:var(--ferrugem); margin-top:.25rem;">
                Pacotes com dois ou mais itens — vinil ou CD.
            </p>
            <div class="filtros">
                <select class="filtro-select" onchange="aplicarFiltro('tipo', this.value)">
                    <option value="">Todos os tipos</option>
                    <option value="vinil" <?= $tipo === 'vinil' ? 'selected' : '' ?>>Discos de Vinil</option>
                    <option value="cd"    <?= $tipo === 'cd'    ? 'selected' : '' ?>>CDs</option>
                    <option value="misto" <?= $tipo === 'misto' ? 'selected' : '' ?>>Misto</option>
                </select>
                <select class="filtro-select" onchange="aplicarFiltro('ordem', this.value)">
                    <option value="recente"   <?= $ordem === 'recente'   ? 'selected' : '' ?>>Mais recentes</option>
                    <option value="preco_asc" <?= $ordem === 'preco_asc' ? 'selected' : '' ?>>Menor preço</option>
                    <option value="preco_desc"<?= $ordem === 'preco_desc'? 'selected' : '' ?>>Maior preço</option>
                </select>
            </div>
        </div>

        <div class="lotes-lista">
            <?php if (empty($lotes)): ?>
                <p style="color: var(--ferrugem);">Nenhum lote disponível no momento.</p>
            <?php else: ?>
                <?php foreach ($lotes as $l): ?>
                <article class="lote-card">
                    <!-- Mosaico das imagens do lote -->
                    <div class="lote-imagens">
                        <?php
                        $imgs = $pdo->prepare("SELECT p.imagem FROM lote_itens li JOIN produtos p ON li.produto_id = p.id WHERE li.lote_id = ? LIMIT 4");
                        $imgs->execute([$l['id']]);
                        $fotos = $imgs->fetchAll(PDO::FETCH_COLUMN);
                        foreach ($fotos as $foto): ?>
                            <img src="<?= htmlspecialchars($foto ?: 'img/placeholder.jpg') ?>" alt="">
                        <?php endforeach; ?>
                    </div>

                    <div class="lote-info">
                        <span class="lote-qtd"><?= $l['qtd_itens'] ?> itens</span>
                        <h3><?= htmlspecialchars($l['titulo']) ?></h3>
                        <p style="font-size:.83rem; color:#666; margin-bottom:.7rem;">
                            <?= htmlspecialchars($l['descricao']) ?>
                        </p>
                        <p style="font-size:.78rem; color:var(--ferrugem); margin-bottom:.5rem;">
                            Vendido por <?= htmlspecialchars($l['vendedor_nome']) ?>
                        </p>
                        <div class="preco_container">
                            <span class="preco">R$ <?= number_format($l['preco'], 2, ',', '.') ?></span>
                        </div>
                        <p class="parcela">12x de R$ <?= number_format($l['preco'] / 12, 2, ',', '.') ?></p>
                        <a href="lote.php?id=<?= $l['id'] ?>" class="btn-comprar" style="display:inline-block; margin-top:.8rem; padding:.5rem 1.2rem; width:auto;">
                            Ver lote
                        </a>
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