<?php
session_start();
require_once 'BD/BD.php';

// Utiliza o get para ler os parâmetros que estão na barra de endereço do navegador
// ?? -> garante valores padrão caso o usuário acabe de entrar na página e nenhum filtro tenha sido clicado ainda
$ordem = $_GET['ordem'] ?? 'recente';
$genero = $_GET['genero'] ?? '';

// Se os discos estiverem ativos e não vendidos
$sql = "SELECT * FROM discos WHERE ativo = TRUE AND vendido = FALSE";

if ($genero) {
    $sql .= " AND genero = " . $pdo->quote($genero);
    // coloca aspas ao redor da string
}

$sql .= match($ordem) { 
    // é como um switch/case. Ele concatena o final da query baseado na escolha do usuário (preço maior, menos...)
    'preco_asc'  => " ORDER BY preco ASC",
    'preco_desc' => " ORDER BY preco DESC",
    default      => " ORDER BY created_at DESC",
};

$produtos = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
$generos  = $pdo->query("SELECT DISTINCT genero FROM discos WHERE genero IS NOT NULL ORDER BY genero")->fetchAll(PDO::FETCH_COLUMN);
// o query executa a instrução diretamente do BD 
// PDO::FETCH_ASSOC -> transforma os produtos em um array onde as chaves são os nomes das colunas das tabelas
// PDO::FETCH_COLUMN) -> em vez de trazer um array tabelado, ele traz uma lista simples contendo apenas os nomes dos gêneros

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetroMusic — Discos de Vinil</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include "header.php"; ?>

    <div class="pagina-listagem">
        <div class="listagem-header">
            <h1>Discos de Vinil</h1>
            <div class="filtros">
                <select class="filtro-select" onchange="aplicarFiltro('genero', this.value)">
                    <option value="">Todos os gêneros</option>
                    <?php // Loop que percorre a lista simples de gêneros gerada pelo FETCH_COLUMN ?>
                    <?php foreach ($generos as $g): ?>
                        <?php // htmlspecialchars protege contra XSS. O operador ternário mantem o gênero selecionado visualmente na tela se corresponder ao filtro ativo ?>
                        <option value="<?= htmlspecialchars($g) ?>" <?= $genero === $g ? 'selected' : '' ?>>
                            <?= htmlspecialchars($g) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select class="filtro-select" onchange="aplicarFiltro('ordem', this.value)">
                    <?php // Verifica qual ordenação está ativa na URL e adiciona o atributo 'selected' na tag html correspondente ?>
                    <option value="recente" <?= $ordem === 'recente' ? 'selected' : '' ?>>Mais recentes</option>
                    <option value="preco_asc" <?= $ordem === 'preco_asc' ? 'selected' : '' ?>>Menor preço</option>
                    <option value="preco_desc" <?= $ordem === 'preco_desc' ? 'selected' : '' ?>>Maior preço</option>
                </select>
            </div>
        </div>

        <div class="produtos-grid">
            <?php // Se a consulta ao banco não trouxer nenhum resultado, o php vai mostrar a tag de erro ?>
            <?php if (empty($produtos)): ?>
                <p style="color: var(--ferrugem); padding: 1rem 0;">Nenhum disco encontrado.</p>
            <?php else: ?>
                <?php // Laço que repete o bloco de código HTML para cada disco retornado do banco de dados
                foreach ($produtos as $p):
                    // calcula o preço final subtraindo a porcentagem caso o produto possua desconto cadastrado maior que zero
                    $preco_final = isset($p['desconto_percent']) && $p['desconto_percent'] > 0
                        ? $p['preco'] * (1 - $p['desconto_percent'] / 100)
                        : $p['preco'];
                ?>
                <article class="produto">
                    <?php // ?: -> define uma imagem padrão de segurança caso o campo no banco esteja vazio ou nulo ?>
                    <img src="<?= htmlspecialchars($p['imagem'] ?: 'img/placeholder.jpg') ?>" alt="<?= htmlspecialchars($p['nome']) ?>">
                    <div class="info_produto">
                        <h3><?= htmlspecialchars($p['nome']) ?></h3>
                        <span class="estadoCapa">Capa: <?= htmlspecialchars($p['estado_capa']) ?></span>
                        <span class="estadoDisco">Disco: <?= htmlspecialchars($p['estado_disco']) ?></span>
                        
                        <?php // Exibe o preço original riscado (preço antigo) apenas se o item possuir um desconto ativo ?>
                        <?php if (isset($p['desconto_percent']) && $p['desconto_percent'] > 0): ?>
                            <p class="precoA">R$ <?= number_format($p['preco'], 2, ',', '.') ?></p>
                        <?php endif; ?>
                        
                        <div class="preco_container">
                            <?php // Formata o valor calculado do preço final para reais ?>
                            <span class="preco">R$ <?= number_format($preco_final, 2, ',', '.') ?></span>
                            <?php // Exibe a etiqueta com a porcentagem de desconto se houver promoção ativa ?>
                            <?php if (isset($p['desconto_percent']) && $p['desconto_percent'] > 0): ?>
                                <span class="desconto"><?= $p['desconto_percent'] ?>% OFF</span>
                            <?php endif; ?>
                        </div>
                        <?php // Realiza a divisão aritmética direta do preço por 12 para mostrar o valor simulado das parcelas ?>
                        <p class="parcela">12x de R$ <?= number_format($preco_final / 12, 2, ',', '.') ?></p>
                        <?php // Constrói o link dinâmico da página interna passando o id do disco e o tipo fixo via parâmetro GET ?>
                        <a href="produto.php?id=<?= $p['id'] ?>&tipo=vinil" class="btn-comprar">Ver produto</a>
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