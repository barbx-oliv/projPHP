<?php
session_start();
require_once 'BD/BD.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $erro = 'Preencha todos os campos.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_id']   = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            header('Location: index.php');
            exit;
        } else {
            $erro = 'E-mail ou senha incorretos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetroMusic — Entrar</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="pagina-auth">
        <div class="auth-form-lado">
            <div class="auth-logo">
                <a href="index.php"><img src="img/retro2.png" alt="RetroMusic"></a>
            </div>

            <div class="auth-box">
                <h2>Bem-vindo de volta</h2>
                <p class="subtitulo">Entre na sua conta para continuar</p>

                <?php if ($erro): ?>
                    <div class="mensagem erro"><?= htmlspecialchars($erro) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="campo">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" placeholder="seu@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>
                    <div class="campo">
                        <label for="senha">Senha</label>
                        <input type="password" id="senha" name="senha" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn-principal">Entrar</button>
                </form>

                <p class="auth-link">Não tem conta? <a href="cadastro.php">Cadastre-se grátis</a></p>
            </div>
        </div>

        <div class="auth-imagem-lado">
            <img src="img/discos_login.webp" alt="Coleção de discos de vinil">
        </div>
    </div>
</body>
</html>
