<?php
session_start();
require_once 'BD/BD.php';

// Captura as variáveis de filtro enviadas via parâmetro GET na URL do navegador
$ordem  = $_GET['ordem'] ?? 'recente';
$genero = $_GET['genero'] ?? '';

// Define a consulta com o BD buscando apenas registros de CDs ativos e disponíveis
$sql = "SELECT * FROM cds WHERE ativo = TRUE AND vendido = FALSE";

// Se um gênero específico foi selecionado, o PHP acrescenta a cláusula de filtro na query
if ($genero) {
    // quote() adiciona aspas de segurança ao redor da string para evitar SQL Injection
    $sql .= " AND genero = " . $pdo->quote($genero);
}

// O match avalia a variável $ordem e concatena a instrução de ordenação correta ao fim do BD
$sql .= match($ordem) {
    'preco_asc'  => " ORDER BY preco ASC",
    'preco_desc' => " ORDER BY preco DESC",
    default      => " ORDER BY created_at DESC",
};

// FETCH_ASSOC para garantir a leitura correta das colunas pelo PHP
$produtos = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// lista de filtros de gêneros diretamente da tabela real de 'cds'
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
                    <?php // Percorre a lista simples de gêneros cadastrados obtida pelo FETCH_COLUMN ?>
                    <?php foreach ($generos as $g): ?>
                        <?php // htmlspecialchars limpa os dados contra ataques XSS e o ternário mantém a tag marcada se for o gênero ativo ?>
                        <option value="<?= htmlspecialchars($g) ?>" <?= $genero === $g ? 'selected' : '' ?>>
                            <?= htmlspecialchars($g) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select class="filtro-select" onchange="aplicarFiltro('ordem', this.value)">
                    <?php // O PHP avalia qual ordenação está ativa na URL e aplica o atributo 'selected' na opção correta ?>
                    <option value="recente" <?= $ordem === 'recente' ? 'selected' : '' ?>>Mais recentes</option>
                    <option value="preco_asc" <?= $ordem === 'preco_asc' ? 'selected' : '' ?>>Menor preço</option>
                    <option value="preco_desc" <?= $ordem === 'preco_desc' ? 'selected' : '' ?>>Maior preço</option>
                </select>
            </div>
        </div>

        <div class="produtos-grid">
            <?php // Se o array de produtos retornado do banco de dados estiver vazio, o PHP renderiza a mensagem de aviso ?>
            <?php if (empty($produtos)): ?>
                <p style="color: var(--ferrugem); padding: 1rem 0;">Nenhum CD encontrado.</p>
            <?php else: ?>
                <?php // Inicia o laço que irá repetir a estrutura HTML para cada CD encontrado no banco
                foreach ($produtos as $p):
                    // Aplica uma operação matemática em tempo de execução para calcular o valor com desconto se a porcentagem for maior que zero
                    $preco_final = $p['desconto_percent'] > 0
                        ? $p['preco'] * (1 - $p['desconto_percent'] / 100)
                        : $p['preco'];
                ?>
                <article class="produto">
                    <?php // ?: -> injeta uma imagem placeholder padrão caso o CD não possua foto no BD ?>
                    <img src="<?= htmlspecialchars($p['imagem'] ?: 'img/placeholder.jpg') ?>" alt="<?= htmlspecialchars($p['nome']) ?>">
                    <div class="info_produto">
                        <h3><?= htmlspecialchars($p['nome']) ?></h3>
                        <span class="estadoCapa">Capa: <?= htmlspecialchars($p['estado_capa']) ?></span>
                        
                        <span class="estadoDisco">CD: <?= htmlspecialchars($p['estado_disco']) ?></span>
                        
                        <?php // Exibe o valor cheio antigo somente se o produto estiver com um desconto ativo ?>
                        <?php if ($p['desconto_percent'] > 0): ?>
                            <p class="precoA">R$ <?= number_format($p['preco'], 2, ',', '.') ?></p>
                        <?php endif; ?>
                        <div class="preco_container">
                            <?php // Converte o valor float do preço final calculado para reais?>
                            <span class="preco">R$ <?= number_format($preco_final, 2, ',', '.') ?></span>
                            <?php // Mostra a porcentagem de desconto promocional caso o produto tenha desconto ?>
                            <?php if ($p['desconto_percent'] > 0): ?>
                                <span class="desconto"><?= $p['desconto_percent'] ?>% OFF</span>
                            <?php endif; ?>
                        </div>
                        <?php // Divide o preço final por 12 para mostrar a simulação das parcelas sem juros ?>
                        <p class="parcela">12x de R$ <?= number_format($preco_final / 12, 2, ',', '.') ?></p>
                        <?php // Constrói dinamicamente o link da página interna anexando o ID do CD atual como parâmetro de URL ?>
                        <a href="produto.php?id=<?= $p['id'] ?>" class="btn-comprar">Ver produto</a>
                    </div>
                </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php // Inclui e processa o encerramento da página com o footer?>
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