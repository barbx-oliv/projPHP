<?php
session_start();
require_once 'BD/BD.php'; 

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    // roda o processo de autenticação se o usuário tiver clicado no botão "Entrar"
    $email = trim($_POST['email'] ?? ''); 
    // o trim limpa os espaços em brancos que foram deixados no inicio ou fim do email
    $senha = $_POST['senha'] ?? '';
    // ?? '' -> captura o email e a senha dos campos

    if (empty($email) || empty($senha)) {
        $erro = 'Preencha todos os campos.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        // 
        $stmt->execute([$email]);
        
        // PDO::FETCH_ASSOC para garantir a leitura correta das chaves de coluna pelo PHP
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            // aqui as senhas são salvas em texto limpo no banco, mas sim em formato de hash criptografado.
            // essa função pega a senha digitada pelo usuário, aplica o cálculo e vê se bate com a criptografia guardada no banco
            $_SESSION['usuario_id']   = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            // se a senha estiver correta, o PHP grava duas informações, sendo o ID e o Nome do usuário
            // Permite ao cabeçalho mudar o bem-vindo, [nome] em outras páginas
            
            // Redireciona para o index ou direto para o perfil se preferir
            header('Location: index.php');
            // Quando salvar a sessão, o php redireciona o usuário para o index.php.
            exit;
            // encerra a execução da página imediatamente, poupando processamento
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
    <style>
        .pagina-auth {
            display: flex;
            min-height: 100vh;
            background-color: var(--creme-claro);
        }
        .auth-form-lado {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem 10%;
        }
        .auth-logo img {
            height: 45px;
            margin-bottom: 2rem;
        }
        .auth-box {
            max-width: 400px;
            width: 100%;
        }
        .auth-box h2 {
            font-family: var(--fonte-serifa);
            color: var(--vinho);
            font-size: 2.2rem;
            margin-bottom: 0.25rem;
        }
        .subtitulo {
            color: var(--texto-suave);
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }
        .auth-link {
            margin-top: 1.5rem;
            font-size: 0.9rem;
            color: var(--texto-suave);
        }
        .auth-link a {
            color: var(--ferrugem);
            font-weight: 600;
        }
        .auth-imagem-lado {
            flex: 1.2;
            position: relative;
            background-color: var(--vinho);
        }
        .auth-imagem-lado img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.85;
        }
        @media (max-width: 768px) {
            .auth-imagem-lado { display: none; }
        }
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="pagina-auth">
        <div class="auth-form-lado">
            <div class="auth-logo">
                <a href="index.php"><img src="img/retro2.png" alt="RetroMusic" height="200"></a>
            </div>

            <div class="auth-box">
                <h2>Bem-vindo de volta</h2>
                <p class="subtitulo">Entre na sua conta para continuar</p>

                <?php if ($erro): ?> 
                    <!-- se os campos estiverem vazios ou a senha e o email não baterem, a variável %erro manda uma mensagem através da função htmlspecialchars($erro) -->
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
                    <button type="submit" class="btn-principal" style="width: 100%; padding: 0.85rem;">Entrar</button>
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