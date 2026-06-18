<?php

// Cria as variáveis que vão guardar os textos de erro ou de sucesso para mostrar na tela
$form_erro    = '';
$form_sucesso = '';

// Verifica se o formulário foi enviado (POST) e se o botão clicado foi o de cadastrar produto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'cadastrar_produto') {
    
    // Pegando os names EXATOS do seu HTML atual:
    $tipo         = $_POST['tipo'] ?? ''; // 'vinil' ou 'cd'
    $nome_completo= trim($_POST['nome'] ?? ''); // trim() tira espaços em branco sobrando no começo e no fim
    $genero       = trim($_POST['genero'] ?? '');
    $ano          = trim($_POST['ano'] ?? '');
    $preco        = (float)($_POST['preco'] ?? 0); // transforma o texto do preço em um número com centavos
    $estado_capa  = $_POST['estado_capa'] ?? '';
    $estado_disco = $_POST['estado_disco'] ?? '';
    $descricao    = trim($_POST['descricao'] ?? '');
    $desconto     = 0; 

    // Validação estrita dos campos obrigatórios
    // O PHP confere se algum campo obrigatório ficou vazio ou se o preço é zero/negativo
    if (empty($nome_completo) || empty($tipo) || $preco <= 0 || empty($estado_capa) || empty($estado_disco) || empty($ano)) {
        $form_erro = 'Por favor, preencha todos os campos obrigatórios (*) e insira um preço válido.';
    } elseif (!in_array($tipo, ['vinil', 'cd'])) {
        // in_array -> garante que o tipo enviado é exatamente 'vinil' ou 'cd', bloqueando qualquer adulteração
        $form_erro = 'Tipo de mídia inválido.';
    } else {
        
        // Upload da imagem para a pasta 
        $imagem = 'img/placeholder.jpg'; // Se o usuário não enviar foto, essa imagem padrão será usada
        
        // Verifica se o arquivo de foto foi enviado pelo formulário sem nenhum erro de upload
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            // Pega a extensão do arquivo e transforma em letras minúsculas
            // (ex: .jpg, .png)
            $ext     = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
            $exts_ok = ['jpg', 'jpeg', 'png', 'webp']; // Lista de formatos aceitos
            
            if (!in_array($ext, $exts_ok)) {
                $form_erro = 'Formato de imagem inválido. Use JPG, PNG ou WEBP.';
            } elseif ($_FILES['imagem']['size'] > 3 * 1024 * 1024) {
                // Bloqueia fotos que tenham tamanho maior que 3 Megabytes
                $form_erro = 'Imagem muito grande (máx. 3 MB).';
            } else {
                // Se a pasta 'img' não existir no seu servidor, o PHP cria ela automaticamente
                if (!is_dir('img')) {
                    mkdir('img', 0777, true);
                }
                // Cria um nome único e aleatório para o arquivo para evitar arquivos com nomes duplicados
                $novo_nome = 'img/' . uniqid('prod_', true) . '.' . $ext;
                // Move a foto da pasta temporária do servidor para a sua pasta definitiva 'img'
                move_uploaded_file($_FILES['imagem']['tmp_name'], $novo_nome);
                $imagem = $novo_nome; // Atualiza a variável com o caminho final da foto
            }
        }

        // Se não houver erros, insere na tabela correta
        if (empty($form_erro)) {
            // O PHP escolhe a tabela 'discos' ou 'cds' dinamicamente baseado na escolha do usuário
            if ($tipo === 'vinil') {
                $sql = "INSERT INTO discos 
                            (usuario_id, nome, genero, ano, preco, estado_capa, estado_disco, descricao, imagem, desconto_percent)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            } else {
                $sql = "INSERT INTO cds 
                            (usuario_id, nome, genero, ano, preco, estado_capa, estado_disco, descricao, imagem, desconto_percent)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            }

            // Prepara a query selecionada acima para evitar ataques de SQL Injection
            $ins = $pdo->prepare($sql);
            // Executa o salvamento mandando todos os dados limpos nas posições dos pontos de interrogação (?)
            $executou = $ins->execute([
                $usuario_id, // Variável global vinda do perfil.php
                $nome_completo,
                $genero,
                $ano,
                $preco,
                $estado_capa,
                $estado_disco,
                $descricao,
                $imagem,
                $desconto
            ]);

            if ($executou) {
                // Redireciona limpando o formulário e atualizando a lista de produtos instantaneamente!
                header("Location: perfil.php?aba=meus-produtos");
                exit; // Interrompe o código aqui para garantir o redirecionamento imediato
            } else {
                $form_erro = 'Erro interno ao salvar no banco de dados. Verifique a estrutura.';
            }
        }
    }
}
?>

<?php // Se houver alguma mensagem de erro nas validações acima, o PHP monta e mostra o bloco de aviso vermelho ?>
<?php if ($form_erro): ?>
    <div class="mensagem erro" style="color: #ba2d32; background: rgba(186, 45, 50, 0.1); padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem; font-weight: 500;">
        <?php // htmlspecialchars() limpa o texto do erro contra tags ou códigos maliciosos antes de exibir na tela ?>
        ⚠️ <?= htmlspecialchars($form_erro) ?>
    </div>
<?php endif; ?>

<div class="painel-box">
    <form action="perfil.php?aba=anunciar" method="POST" enctype="multipart/form-data" style="margin-top: 1.5rem;">
        <input type="hidden" name="acao" value="cadastrar_produto">

        <div class="campo">
            <label for="tipo">Tipo de Mídia *</label>
            <select name="tipo" id="tipo" required>
                <option value="vinil">Disco de Vinil</option>
                <option value="cd">CD / DVD </option>
            </select>
        </div>

        <div class="campo">
            <label for="nome">Nome do Álbum *</label>
            <input type="text" name="nome" id="nome" required placeholder="Ex: Portals — Melanie Martinez">
        </div>

        <div class="campo">
            <label for="genero">Gênero Musical *</label>
            <input type="text" name="genero" id="genero" required placeholder="Ex: Pop/Alternativo, Rock, Jazz">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="campo">
                <label for="ano">Ano de Lançamento *</label>
                <input type="text" name="ano" id="ano" maxlength="4" required placeholder="Ex: 2023">
            </div>
            <div class="campo">
                <label for="preco">Preço (R$) *</label>
                <input type="number" name="preco" id="preco" step="0.01" min="0" required placeholder="0.00">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="campo">
                <label for="estado_capa">Estado da Capa *</label>
                <select name="estado_capa" id="estado_capa" required>
                    <option value="Mint (M)">Mint (M) - Impecável</option>
                    <option value="Near Mint (NM)">Near Mint (NM) - Quase Perfeito</option>
                    <option value="Muito Bom (VG+)">Muito Bom (VG+)</option>
                    <option value="Bom (VG)">Bom (VG)</option>
                    <option value="Regular (G)">Regular (G)</option>
                </select>
            </div>
            <div class="campo">
                <label for="estado_disco">Estado do Disco / Mídia *</label>
                <select name="estado_disco" id="estado_disco" required>
                    <option value="Mint (M)">Mint (M) - Perfeito, sem riscos</option>
                    <option value="Near Mint (NM)">Near Mint (NM) - Marcas imperceptíveis</option>
                    <option value="Muito Bom (VG+)">Muito Bom (VG+)</option>
                    <option value="Bom (VG)">Bom (VG)</option>
                    <option value="Regular (G)">Regular (G)</option>
                </select>
            </div>
        </div>

        <div class="campo">
            <label for="descricao">Descrição do Item</label>
            <textarea name="descricao" id="descricao" rows="4" placeholder="Escreva detalhes sobre encarte, edição, cor do vinil ou conservação..."></textarea>
        </div>

        <div class="campo">
            <label for="imagem">Foto do Produto</label>
            <input type="file" name="imagem" id="imagem" accept="image/*">
            <small style="color: var(--texto-suave); display:block; margin-top:.25rem;">Formatos aceitos: JPG, PNG, WEBP.</small>
        </div>

        <button type="submit" class="btn-principal" style="width: 100%; margin-top: 1rem; cursor: pointer;">Publicar Anúncio</button>
    </form>
</div>