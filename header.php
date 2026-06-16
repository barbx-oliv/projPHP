<?php
// Garante que a sessão está ativa para verificar o login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica qual é a página atual para aplicar o efeito de aba ativa
$pagina_atual = basename($_SERVER['PHP_SELF']);
?>
<header class="site-header">
    <div class="header-main">
        <div class="header-container">
        <a href="index.php" class="header-logo-block">
            <img src="img/retroicon.png" alt="RetroMusic Logo" class="logo-oficial-img">
        </a>
            
            <div class="header-busca">
                <form action="discos.php" method="GET">
                    <input type="text" name="busca" placeholder="Pesquisar álbuns, artistas, CDs...">
                </form>
            </div>
            
            <div class="header-usuario">
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <span class="usuario-logado">Olá, <a href="perfil.php"><strong><?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Colecionador') ?></strong></a></span>
                    <span class="divisor">|</span>
                    <a href="logout.php" class="auth-btn logout">Sair</a>
                <?php else: ?>
                    <a href="login.php" class="auth-btn">iniciar sessão</a>
                    <span class="divisor">|</span>
                    <a href="cadastro.php" class="auth-btn">cadastro</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <nav class="header-nav">
        <div class="header-container">
            <ul class="nav-links">
                <li><a href="index.php" <?= $pagina_atual === 'index.php' ? 'class="ativo"' : '' ?>>Inicio</a></li>
                <li><a href="discos.php" <?= $pagina_atual === 'discos.php' || $pagina_atual === 'perfil.php' ? 'class="ativo"' : '' ?>>Discos</a></li>
                <li><a href="lotes.php" <?= $pagina_atual === 'cd.php' ? 'class="ativo"' : '' ?>>CD's</a></li>
                <li><a href="vitrolas.php" <?= $pagina_atual === 'lotes.php' ? 'class="ativo"' : '' ?>>Lotes</a></li>
                <li><a href="leilao.php" <?= $pagina_atual === 'perfil.php' ? 'class="ativo"' : '' ?>>Perfil</a></li>
            </ul>
        </div>
    </nav>
</header>