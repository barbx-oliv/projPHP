<?php
session_start();
require_once 'BD/BD.php';

// Inicializa as variáveis de controle de mensagens que serão exibidas na tela
$erro   = '';
$sucesso = '';

// Verifica se a requisição atual enviou um formulário utilizando o método HTTP POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura as strings enviadas do formulário, removendo espaços nas pontas com o trim()
    $nome   = trim($_POST['nome'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    // Captura as senhas sem o trim() para permitir que espaços façam parte da senha original se o usuário desejar
    $senha  = $_POST['senha'] ?? '';
    $conf   = $_POST['confirmar_senha'] ?? '';

    // Inicia a cadeia de validações dos dados inseridos no formulário antes de salvá-los
    if (empty($nome) || empty($email) || empty($senha) || empty($conf)) {
        $erro = 'Preencha todos os campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // filter_var -> verifica se o formato de texto inserido corresponde a um endereço de e-mail real e válido
        $erro = 'E-mail inválido.';
    } elseif (strlen($senha) < 6) {
        // strlen -> conta a quantidade de caracteres da string para garantir o requisito mínimo de tamanho da senha
        $erro = 'A senha precisa ter pelo menos 6 caracteres.';
    } elseif ($senha !== $conf) {
        // Validação básica que garante a perfeita igualdade na redigitação da senha
        $erro = 'As senhas não coincidem.';
    } else {
        // Verifica duplicidade
        // PHP verifica se o email ja existe na tabela de usuarios do BD
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        
        // fetch() -> retorna verdadeiro se encontrar alguma linha correspondente no BD
        if ($stmt->fetch()) {
            $erro = 'Este e-mail já está cadastrado.';
        } else {
            // password_hash -> cria um hash criptografado unidirecional seguro 
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            // Prepara a instrução SQL de inserção com placeholders para blindar o banco contra ataques de SQL Injection
            $ins  = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
            // Executa passando as variáveis limpas e a senha criptografada em formato de array
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

                <?php // O PHP testa se a string de erro foi preenchida para gerar e desenhar o container de aviso vermelho na tela ?>
                <?php if ($erro): ?>
                    <?php // htmlspecialchars é usado para neutralizar códigos maliciosos inseridos acidentalmente no retorno ?>
                    <div class="mensagem erro"><?= htmlspecialchars($erro) ?></div>
                <?php endif; ?>
                
                <?php // O PHP testa se a string de sucesso está preenchida para gerar o container verde de confirmação ?>
                <?php if ($sucesso): ?>
                    <?php // Aqui a exibição curta comum é usada sem o htmlspecialchars para permitir a renderização do link <a> criado no topo ?>
                    <div class="mensagem sucesso"><?= $sucesso ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="campo">
                        <label for="nome">Nome completo</label>
                        <?php // O PHP lê o array $_POST para devolver o valor que o usuário já tinha digitado, evitando que ele perca o que escreveu em caso de erro ?>
                        <input type="text" id="nome" name="nome" placeholder="Seu nome" value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" required>
                    </div>
                    <div class="campo">
                        <label for="email">E-mail</label>
                        <?php // Devolve o e-mail digitado anteriormente aplicando a filtragem necessária contra vulnerabilidades XSS ?>
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