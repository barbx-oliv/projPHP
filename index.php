<?php
session_start();
require_once 'BD/BD.php';

try {
    // Código limpo: o campo 'tipo' foi removido de ambas as consultas do UNION
    $ofertas = $pdo->query("
        SELECT id, nome, imagem, preco, estado_capa, estado_disco AS estado_midia, 10 AS desconto_percent, created_at 
        FROM discos
        WHERE ativo = TRUE AND vendido = FALSE
        
        UNION ALL
        
        SELECT id, nome, imagem, preco, estado_capa, estado_disco AS estado_midia, 10 AS desconto_percent, created_at 
        FROM cds
        WHERE ativo = TRUE AND vendido = FALSE
        
        ORDER BY created_at DESC
        LIMIT 8
    ")->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // Caso dê algum erro no banco, criamos uma lista vazia para exibir os produtos de exemplo em vez de travar a tela
    $ofertas = []; 
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetroMusic — Discos & CDs</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include "header.php"; ?>

    <div class="banner">
        <img src="img/vitrine_discos.jpg" alt="Vitrine de discos">
        <div class="banner-overlay">
            <h1>Sua coleção<br><em>começa aqui.</em></h1>
            <p>Vinil, CDs e lotes raros — compre e venda com quem entende de música.</p>
        </div>
    </div>

    <h2 class="secao-titulo">Navegue por categoria</h2>
    <div class="cards">
        <a href="disco.php" class="card-categoria">
            <img src="img/categoria_disco.jpg" alt="Discos de Vinil">
            <p>Discos de Vinil</p>
        </a>
        <a href="cd.php" class="card-categoria">
            <img src="img/categoria_cd.jpg" alt="CDs">
            <p>CD's</p>
        </a>
        <a href="lotes.php" class="card-categoria">
            <img src="img/categoria_lote.jpg" alt="Lotes">
            <p>Lotes</p>
        </a>
        <a href="perfil.php" class="card-categoria">
            <img src="img/categoria_perfil.jpg" alt="Meu Perfil">
            <p>Meu Perfil</p>
        </a>
    </div>

    <section class="secao-ofertas">
        <div class="oferta-header">
            <h2>Ofertas</h2>
            <span class="tag-oferta">Promoções</span>
        </div>

        <div class="produtos-grid">
            <?php if (empty($ofertas)): ?>
                <article class="produto">
                    <img src="img/disco_melaniePortals.jpg" alt="Portals - Melanie Martinez">
                    <div class="info_produto">
                        <h3>Portals — Melanie Martinez</h3>
                        <span class="estadoCapa">Capa: Ótimo</span>
                        <span class="estadoDisco">Disco: Muito Bom</span>
                        <p class="precoA">R$ 300,99</p>
                        <div class="preco_container">
                            <span class="preco">R$ 90,20</span>
                            <span class="desconto">30% OFF</span>
                        </div>
                        <p class="parcela">12x de R$ 7,52</p>
                        <a href="login.php" class="btn-comprar">Ver produto</a>
                    </div>
                </article>
                <article class="produto">
                    <img src="img/disco_Kiss.jpg" alt="KISS">
                    <div class="info_produto">
                        <h3>Alive! — KISS</h3>
                        <span class="estadoCapa">Capa: Bom</span>
                        <span class="estadoDisco">Disco: Bom</span>
                        <p class="precoA">R$ 180,00</p>
                        <div class="preco_container">
                            <span class="preco">R$ 126,00</span>
                            <span class="desconto">30% OFF</span>
                        </div>
                        <p class="parcela">12x de R$ 10,50</p>
                        <a href="login.php" class="btn-comprar">Ver produto</a>
                    </div>
                </article>
            <?php else: ?>
                <?php foreach ($ofertas as $p):
                    $preco_com_desc = $p['preco'] * (1 - $p['desconto_percent'] / 100);
                ?>
                <article class="produto">
                    <img src="<?= htmlspecialchars($p['imagem']) ?>" alt="<?= htmlspecialchars($p['nome']) ?>">
                    <div class="info_produto">
                        <h3><?= htmlspecialchars($p['nome']) ?></h3>
                        <span class="estadoCapa">Capa: <?= htmlspecialchars($p['estado_capa']) ?></span>
                        
                        <span class="estadoDisco">Mídia: <?= htmlspecialchars($p['estado_midia']) ?></span>
                        
                        <p class="precoA">R$ <?= number_format($p['preco'], 2, ',', '.') ?></p>
                        <div class="preco_container">
                            <span class="preco">R$ <?= number_format($preco_com_desc, 2, ',', '.') ?></span>
                            <span class="desconto"><?= $p['desconto_percent'] ?>% OFF</span>
                        </div>
                        <p class="parcela">12x de R$ <?= number_format($preco_com_desc / 12, 2, ',', '.') ?></p>
                        <a href="produto.php?id=<?= $p['id'] ?>" class="btn-comprar">Ver produto</a>
                    </div>
                </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <?php include "footer.php"; ?>
</body>
</html>