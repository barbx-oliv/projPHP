<?php
// includes/form_produto.php
// Incluído dentro de perfil.php na aba "anunciar"

$form_erro    = '';
$form_sucesso = '';

// 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'cadastrar_produto') {
    
    // Pegando os names EXATOS do seu HTML atual:
    $tipo         = $_POST['tipo'] ?? ''; // 'vinil' ou 'cd'
    $nome_completo= trim($_POST['nome'] ?? '');
    $genero       = trim($_POST['genero'] ?? '');
    $ano          = trim($_POST['ano'] ?? '');
    $preco        = (float)($_POST['preco'] ?? 0);
    $estado_capa  = $_POST['estado_capa'] ?? '';
    $estado_disco = $_POST['estado_disco'] ?? '';
    $descricao    = trim($_POST['descricao'] ?? '');
    $desconto     = 0; // Valor padrão já que tiramos o campo do HTML

    // Validação estrita dos campos obrigatórios
    if (empty($nome_completo) || empty($tipo) || $preco <= 0 || empty($estado_capa) || empty($estado_disco) || empty($ano)) {
        $form_erro = 'Por favor, preencha todos os campos obrigatórios (*) e insira um preço válido.';
    } elseif (!in_array($tipo, ['vinil', 'cd'])) {
        $form_erro = 'Tipo de mídia inválido.';
    } else {
        
        // Upload da imagem para a pasta 
        $imagem = 'img/placeholder.jpg';
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            $ext     = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
            $exts_ok = ['jpg', 'jpeg', 'png', 'webp'];
            
            if (!in_array($ext, $exts_ok)) {
                $form_erro = 'Formato de imagem inválido. Use JPG, PNG ou WEBP.';
            } elseif ($_FILES['imagem']['size'] > 3 * 1024 * 1024) {
                $form_erro = 'Imagem muito grande (máx. 3 MB).';
            } else {
                if (!is_dir('img')) {
                    mkdir('img', 0777, true);
                }
                $novo_nome = 'img/' . uniqid('prod_', true) . '.' . $ext;
                move_uploaded_file($_FILES['imagem']['tmp_name'], $novo_nome);
                $imagem = $novo_nome;
            }
        }

        // Se não houver erros, insere na tabela correta
        if (empty($form_erro)) {
            if ($tipo === 'vinil') {
                $sql = "INSERT INTO discos 
                            (usuario_id, nome, genero, ano, preco, estado_capa, estado_disco, descricao, imagem, desconto_percent)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            } else {
                $sql = "INSERT INTO cds 
                            (usuario_id, nome, genero, ano, preco, estado_capa, estado_disco, descricao, imagem, desconto_percent)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            }

            $ins = $pdo->prepare($sql);
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
                exit;
            } else {
                $form_erro = 'Erro interno ao salvar no banco de dados. Verifique a estrutura.';
            }
        }
    }
}
?>

<?php if ($form_erro): ?>
    <div class="mensagem erro" style="color: #ba2d32; background: rgba(186, 45, 50, 0.1); padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem; font-weight: 500;">
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