<?php
session_start();
require_once 'BD/BD.php';

// Redireciona se não estiver logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Aba ativa 
$aba = $_GET['aba'] ?? 'meus-produtos';

// Dados do usuário
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Atualiza perfil 
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
            $sucesso = 'Perfil atualizado com sucesso!';
        }
    }
}

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

// Meus produtos para venda
$meus_produtos = $pdo->prepare($query_produtos);
$meus_produtos->execute([$usuario_id, $usuario_id]);
$produtos = $meus_produtos->fetchAll(PDO::FETCH_ASSOC);

// Contagem
$total_produtos = count($produtos);
$total_vendidos = array_reduce($produtos, fn($c, $p) => $c + ($p['vendido'] ? 1 : 0), 0);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetroMusic — Meu Perfil</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include "header.php"; ?>

    <div class="pagina-perfil">
        <div class="perfil-grid">

            <aside class="perfil-card">
                <div class="avatar">
                    <?= !empty($usuario['nome']) ? mb_strtoupper(mb_substr($usuario['nome'], 0, 1)) : 'U' ?>
                </div>
                <h2><?= htmlspecialchars($usuario['nome'] ?? 'Colecionador') ?></h2>
                <p class="email"><?= htmlspecialchars($usuario['email'] ?? '') ?></p>

                <div class="perfil-stats">
                    <div class="stat">
                        <div class="stat-num"><?= $total_produtos ?></div>
                        <div class="stat-label">Anúncios</div>
                    </div>
                    <div class="stat">
                        <div class="stat-num"><?= $total_vendidos ?></div>
                        <div class="stat-label">Vendidos</div>
                    </div>
                </div>

                <ul class="perfil-menu">
                    <li>
                        <a href="?aba=meus-produtos" <?= $aba === 'meus-produtos' ? 'class="ativo"' : '' ?>>
                             Meus produtos
                        </a>
                    </li>
                    <li>
                        <a href="?aba=anunciar" <?= $aba === 'anunciar' ? 'class="ativo"' : '' ?>>
                             Anunciar item
                        </a>
                    </li>
                    <li>
                        <a href="?aba=editar-perfil" <?= $aba === 'editar-perfil' ? 'class="ativo"' : '' ?>>
                             Editar perfil
                        </a>
                    </li>
                    <li><a href="logout.php" style="color: var(--erro);"> Sair</a></li>
                </ul>
            </aside>

            <div class="perfil-conteudo">

                <?php if ($aba === 'meus-produtos'): ?>
                <div class="painel-box">
                    <h3>Meus Itens à Venda</h3>
                    <?php if (empty($produtos)): ?>
                        <p style="color: var(--texto-suave); font-size:.95rem;">
                            Você ainda não cadastrou produtos. <a href="?aba=anunciar" style="color:var(--ferrugem); font-weight:600;">Comece a vender agora →</a>
                        </p>
                    <?php else: ?>
                    <table class="tabela-produtos">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Tipo</th>
                                <th>Preço</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($produtos as $p): ?>
                            <tr>
                                <td style="font-weight: 500;"><?= htmlspecialchars($p['nome']) ?></td>
                                <td><?= $p['tipo'] === 'vinil' ? ' Vinil' : ' CD' ?></td>
                                <td style="color: var(--ferrugem); font-weight: 600;">R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
                                <td>
                                    <?php if ($p['vendido']): ?>
                                        <span class="badge badge-pausado">Vendido</span>
                                    <?php elseif ($p['ativo']): ?>
                                        <span class="badge badge-ativo">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge badge-pausado">Pausado</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="editar_produto.php?id=<?= $p['id'] ?>&tipo=<?= $p['tipo'] ?>" style="color:var(--vinho); font-weight:600; font-size: .9rem;">Editar</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>

                <?php elseif ($aba === 'anunciar'): ?>
                <div class="painel-box">
                    <h3>Anunciar um Novo Item</h3>
                    <?php 
                    include 'includes/form_produto.php'; 
                    ?>
                </div>

                <?php elseif ($aba === 'editar-perfil'): ?>
                <div class="painel-box">
                    <h3>Configurações da Conta</h3>

                    <?php if ($sucesso): ?><div class="mensagem sucesso"><?= htmlspecialchars($sucesso) ?></div><?php endif; ?>
                    <?php if ($erro): ?><div class="mensagem erro"><?= htmlspecialchars($erro) ?></div><?php endif; ?>

                    <form method="POST" style="max-width: 500px;">
                        <input type="hidden" name="acao" value="atualizar_perfil">
                        <div class="campo">
                            <label for="nome">Nome Completo</label>
                            <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($usuario['nome'] ?? '') ?>" required>
                        </div>
                        <div class="campo">
                            <label>E-mail Cadastrado</label>
                            <input type="email" value="<?= htmlspecialchars($usuario['email'] ?? '') ?>" disabled style="background-color: var(--creme-claro); cursor: not-allowed; opacity: 0.7;">
                        </div>
                        <button type="submit" class="btn-principal">Salvar Alterações</button>
                    </form>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <?php include "footer.php"; ?>
</body>
</html>