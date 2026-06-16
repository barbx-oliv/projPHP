<?php
session_start();
require_once 'BD/BD.php';

$ordem = $_GET['ordem'] ?? 'recente';
// ?? '' -> Valores padrão. Se o usuário acabou de entrar e não clicou em nada, o php ordena por itens mais recentes e mostra todos os produtos juntos
$tipo  = $_GET['tipo'] ?? ''; 
// aqui o php vai ler a url para saber se o usuário escolheiu apenas vinil, apenas vd, ou se mudou a ordenação 
// por exemplo - menor preço

// Query unificada (UNION) que busca dados de 'lotedisco' e 'lotecd' individualmente
// cria uma coluna tipo_lote para o php saber a origem de cada registro 
$sql = "
    SELECT id, usuario_id, titulo, descricao, preco, imagem, created_at, 'vinil' AS tipo_lote 
    FROM lotedisco 
    WHERE ativo = TRUE AND vendido = FALSE
    
    UNION ALL 
    
    SELECT id, usuario_id, titulo, descricao, preco, imagem, created_at, 'cd' AS tipo_lote 
    FROM lotecd 
    WHERE ativo = TRUE AND vendido = FALSE
";

// Se o usuário filtrar por tipo, traz apenas uma delas
if ($tipo === 'vinil') {
    $sql = "SELECT id, usuario_id, titulo, descricao, preco, imagem, created_at, 'vinil' AS tipo_lote 
            FROM lotedisco WHERE ativo = TRUE AND vendido = FALSE";
} elseif ($tipo === 'cd') {
    $sql = "SELECT id, usuario_id, titulo, descricao, preco, imagem, created_at, 'cd' AS tipo_lote 
            FROM lotecd WHERE ativo = TRUE AND vendido = FALSE";
} // Se o usuário filtrar "Lotes de Vinil", o php sobescrebe a variável $sql para fazer a busca apenas na tabela correspondente

// Aplicação da ordenação por cima do resultado da query unificada
$sqlWrapper = "SELECT lista.*, u.nome AS vendedor_nome FROM ($sql) AS lista
--  envelopa a busca anterior e faz um JOIN com a tabela de usuarios.
-- serve para trazer o nome do vendedor de cada lote de uma vez só 
               JOIN usuarios u ON lista.usuario_id = u.id";

$sqlWrapper .= match($ordem) { 
    // é como um switch/case. Ele concatena o final da query baseado na escolha do usuário (preço maior, menos...)
    'preco_asc'  => " ORDER BY lista.preco ASC",
    'preco_desc' => " ORDER BY lista.preco DESC",
    default      => " ORDER BY lista.created_at DESC",
};

$lotes = $pdo->query($sqlWrapper)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetroMusic — Lotes Promocionais</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include "header.php"; ?>

    <div class="pagina-listagem">
        <div class="listagem-header">
            <div>
                <h1>Lotes</h1>
                <p style="font-size:.9rem; color:var(--texto-suave); margin-top:.25rem;">
                    Explore coleções exclusivas de CDs e Discos de Vinil criadas pelos nossos usuários.
                </p>
            </div>
            <div class="filtros">
                <select class="filtro-select" onchange="aplicarFiltro('tipo', this.value)">
                    <option value="">Todos os Lotes</option>
                    <option value="vinil" <?= $tipo === 'vinil' ? 'selected' : '' ?>>Lotes de Vinil</option>
                    <option value="cd"    <?= $tipo === 'cd'    ? 'selected' : '' ?>>Lotes de CDs</option>
                </select>
                <select class="filtro-select" onchange="aplicarFiltro('ordem', this.value)">
                    <option value="recente"   <?= $ordem === 'recente'   ? 'selected' : '' ?>>Mais recentes</option>
                    <option value="preco_asc" <?= $ordem === 'preco_asc' ? 'selected' : '' ?>>Menor preço</option>
                    <option value="preco_desc"<?= $ordem === 'preco_desc'? 'selected' : '' ?>>Maior preço</option>
                </select>
            </div>
        </div>

        <div class="produtos-grid">
            <?php if (empty($lotes)): ?>
                <p style="color: var(--ferrugem); padding: 1rem 0;">Nenhum lote encontrado nesta categoria.</p>
            <?php else: ?>
                <?php foreach ($lotes as $l): ?>
                <article class="produto">
                    
                    <div style="background: var(--creme-escuro); height: 260px; overflow: hidden; position: relative;">
                        <?php
                        $fotos = [];

                        // Verifica se o lote possui uma imagem própria válida cadastrada na tabela dele
                        if (!empty($l['imagem']) && $l['imagem'] !== 'img/placeholder.jpg') {
                            $fotos[] = $l['imagem'];
                        } else {
                            // Caso seja o placeholder padrão, buscamos os itens usando suas tabelas e colunas reais
                            if ($l['tipo_lote'] === 'vinil') {
                                $stmt_img = $pdo->prepare("
                                    SELECT d.imagem FROM lotedisco_itens ldi
                                    JOIN discos d ON ldi.discos_id = d.id 
                                    WHERE ldi.lotedisco_id = ? LIMIT 4
                                ");
                                $stmt_img->execute([$l['id']]);
                                $fotos = $stmt_img->fetchAll(PDO::FETCH_COLUMN);
                            } else {
                                $stmt_img = $pdo->prepare("
                                    SELECT c.imagem FROM lotecd_itens lci
                                    JOIN cds c ON lci.cds_id = c.id 
                                    WHERE lci.lotecd_id = ? LIMIT 4
                                ");
                                $stmt_img->execute([$l['id']]);
                                $fotos = $stmt_img->fetchAll(PDO::FETCH_COLUMN);
                            }
                        }

                        // Caso tudo falhe, usa o placeholder final
                        if (empty($fotos)) {
                            $fotos[] = 'img/placeholder.jpg';
                        }

                        // Se encontrou apenas uma imagem (a do próprio lote ou de 1 item), mostra em tamanho cheio
                        if (count($fotos) === 1): ?>
                            <img src="<?= htmlspecialchars($fotos[0]) ?>" style="width:100%; height:100%; object-fit:cover;" alt="">
                        <?php else: 
                            // Se encontrou várias imagens de itens, monta o mosaico dinâmico dividindo o container
                            ?>
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 2px; height: 100%;">
                                <?php foreach ($fotos as $foto): ?>
                                    <img src="<?= htmlspecialchars($foto ?: 'img/placeholder.jpg') ?>" style="width:100%; height:130px; object-fit:cover;" alt="">
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="info_produto">
                        <span class="tag-oferta" style="align-self: flex-start; margin-bottom: .5rem; background-color: <?= $l['tipo_lote'] === 'vinil' ? 'var(--vinho)' : 'var(--ferrugem)' ?>;">
                            Lote <?= $l['tipo_lote'] === 'vinil' ? ' | Vinil' : ' | CD' ?>
                        </span>
                        
                        <h3><?= htmlspecialchars($l['titulo']) ?></h3>
                        <p style="font-size:.85rem; color: var(--texto-suave); margin-bottom:.5rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            <?= htmlspecialchars($l['descricao']) ?>
                        </p>
                        <p style="font-size:.8rem; color:var(--texto-escuro); font-weight: 500; margin-bottom:.5rem;">
                            Por: <?= htmlspecialchars($l['vendedor_nome']) ?>
                        </p>
                        <div class="preco_container">
                            <span class="preco">R$ <?= number_format($l['preco'], 2, ',', '.') ?></span>
                        </div>
                        <p class="parcela">12x de R$ <?= number_format($l['preco'] / 12, 2, ',', '.') ?></p>
                        
                        <a href="lote.php?id=<?= $l['id'] ?>&tipo=<?= $l['tipo_lote'] ?>" class="btn-comprar">Ver lote completo</a>
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