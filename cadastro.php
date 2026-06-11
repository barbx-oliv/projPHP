
<?php
session_start();
require_once 'BD/BD.php';

$erro   = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome   = trim($_POST['nome'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $senha  = $_POST['senha'] ?? '';
    $conf   = $_POST['confirmar_senha'] ?? '';

    if (empty($nome) || empty($email) || empty($senha) || empty($conf)) {
        $erro = 'Preencha todos os campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'E-mail inválido.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha precisa ter pelo menos 6 caracteres.';
    } elseif ($senha !== $conf) {
        $erro = 'As senhas não coincidem.';
    } else {
        // Verifica duplicidade
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $erro = 'Este e-mail já está cadastrado.';
        } else {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $ins  = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
            $ins->execute([$nome, $email, $hash]);

            $sucesso = 'Conta criada com sucesso! <a href="login.php">Faça login</a>.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetroMusic — Cadastro</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="pagina-auth">
        <div class="auth-form-lado">
            <div class="auth-logo">
                <a href="index.php"><img src="img/retro2.png" alt="RetroMusic"></a>
            </div>

            <div class="auth-box">
                <h2>Criar conta</h2>
                <p class="subtitulo">Compre e venda discos e CDs</p>

                <?php if ($erro): ?>
                    <div class="mensagem erro"><?= htmlspecialchars($erro) ?></div>
                <?php endif; ?>
                <?php if ($sucesso): ?>
                    <div class="mensagem sucesso"><?= $sucesso ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="campo">
                        <label for="nome">Nome completo</label>
                        <input type="text" id="nome" name="nome" placeholder="Seu nome" value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" required>
                    </div>
                    <div class="campo">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" placeholder="seu@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>
                    <div class="campo">
                        <label for="senha">Senha</label>
                        <input type="password" id="senha" name="senha" placeholder="Mínimo 6 caracteres" required>
                    </div>
                    <div class="campo">
                        <label for="confirmar_senha">Confirmar senha</label>
                        <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Repita a senha" required>
                    </div>
                    <button type="submit" class="btn-principal">Criar conta</button>
                </form>

                <p class="auth-link">Já tem conta? <a href="login.php">Entrar</a></p>
            </div>
        </div>

        <div class="auth-imagem-lado">
            <img src="img/discos_cadastro.jpg" alt="Discos de vinil coloridos">
        </div>
    </div>
</body>
</html>
