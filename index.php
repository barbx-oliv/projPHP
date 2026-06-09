<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetroMusic</title>
</head>
<body>
    <?php include "header.php"; ?>
    <br>
    <div class="banner">
        <img src="img/vitrine_discos.jpg" alt="banner">
    </div>
    <br>
    <section>
        <div class="cards">
            <div class="disco">
                <img src="" alt="disco">
                <p><a href="disco.php">Discos</a></p>
            </div>
            <div class="cd">
                <img src="" alt="cd">
                <p><a href="cd.php">CD's</a></p>
            </div>
            <div class="perfil">
                <img src="" alt="perfil">
                <p><a href="perfil.php">Perfil</a></p>
            </div>
            </div>
        </div>
    </section>
    <div class="oferta">
        <h2>Ofertas</h2>
    </div>
    <main>
        <article class="produto">
            <img src="img/disco_melaniePortals.jpg" alt="discoPortals" style="height: 400px;">
            <div class="info_produto">
                <h3>Disco Portals - VOID</h3>
                <p class="precoA">R$ 300,99</p>
                <p class="estadoCapa">Estado da capa</p>
                <p class="estadoDisco">Estado do disco</p>
                <div class="preco_container">
                    <span class="preco">R$ 90,2</span>
                    <span class="desconto">30% OFF</span>
                </div>
                <p class="parcela">12x R$ 7,52</p>
            </div>
        </article>
        <article class="produto">
            <img src="img/disco_Kiss.jpg" alt="discoKiss" style="height: 400px;">
            <div class="info_produto">
                <h3>Disco Portals - </h3>
                <p class="precoA">R$ 300,99</p>
                <p class="estadoCapa">Estado da capa</p>
                <p class="estadoDisco">Estado do disco</p>
                <div class="preco_container">
                    <span class="preco">R$ 90,2</span>
                    <span class="desconto">30% OFF</span>
                </div>
                <p class="parcela">12x R$ 7,52</p>
            </div>
        </article>
    </main>
    <?php include "footer.php"?>
</body>
</html>