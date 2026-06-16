<?php
session_start();
require_once 'BD/BD.php'; // conexão com o banco de dados

try { // Se o banco funcionar corretamente, ele vai carregar os produtos com base nas tabelas do BD
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
    // Caso dê algum erro no BD, cria uma lista vazia para exibir os produtos de exemplo em vez de travar a tela
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
            <h1>Sua coleção<br><em>começa aqui!</em></h1>
            <p>Vinil, CDs e lotes raros — compre e venda com quem entende de música ♡</p>
        </div>
    </div>

    <h2 class="secao-titulo">Categorias</h2>
    <div class="cards">
        <a href="disco.php" class="card-categoria">
            <img src="img/discos_index.jpg" alt="Discos de Vinil">
            <p>Discos de Vinil</p>
        </a>
        <a href="cd.php" class="card-categoria">
            <img src="img/cds_index.jpg" alt="CDs">
            <p>CD's</p>
        </a>
        <a href="lotes.php" class="card-categoria">
            <img src="img/lotes_index.jpg" alt="Lotes">
            <p>Lotes</p>
        </a>
        <a href="perfil.php" class="card-categoria">
            <img src="img/perfil_index.jpg" alt="Meu Perfil">
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
                // vê produto por produtor que veio do BD e gera o card de forma autpmatica.
                // por exemplo, se tiver 2 produtos, desenha 2 cards. 8 produtos, 8 cards... Assim por diante
                // dessa forma não precisa escrever o html de cada card
                    $preco_com_desc = $p['preco'] * (1 - $p['desconto_percent'] / 100);
                    // aqui o PHP vai fazer um cálculo de matemática para pega o preço do BD e calcular o desconto de 10% em tempo real.
                    // defeito - coloca desconto em todos os produtos em vez do usuário decidir. Preciso ajeitar dps
                ?>
                <article class="produto">
                    <img src="<?= htmlspecialchars($p['imagem']) ?>" alt="<?= htmlspecialchars($p['nome']) ?>">
                    <!-- limpa os textos vindos do BD, impedindo ataques de injeção de scripts maliciosos no navegador do cliente -->
                    <div class="info_produto">
                        <h3><?= htmlspecialchars($p['nome']) ?></h3>
                        <span class="estadoCapa">Capa: <?= htmlspecialchars($p['estado_capa']) ?></span>
                        
                        <span class="estadoDisco">Mídia: <?= htmlspecialchars($p['estado_midia']) ?></span>
                        
                        <p class="precoA">R$ <?= number_format($p['preco'], 2, ',', '.') ?></p>
                        <div class="preco_container">
                            <span class="preco">R$ <?= number_format($preco_com_desc, 2, ',', '.') ?></span>
                            <!-- Formata os números do valor do produto para reais -->
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