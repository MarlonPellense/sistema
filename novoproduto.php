<?php
include('conexao.php'); // Conexão com o banco de dados
require_once 'protect.php'; // Proteção de acesso

$erro = '';
$sucesso = '';

// Define o caminho absoluto para a pasta uploads
$upload_dir = __DIR__ . '/uploads/';
$upload_path = 'uploads/'; // Caminho relativo para salvar no banco

// Verifica se a pasta existe e tem permissões adequadas
if (!is_dir($upload_dir)) {
    if (!mkdir($upload_dir, 0755, true)) {
        $erro = "Não foi possível criar a pasta de uploads.";
    }
} elseif (!is_writable($upload_dir)) {
    $erro = "A pasta uploads não tem permissões de escrita.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$erro) {
    $nome_produto = trim($_POST['nome_produto']);
    $valor_compra = floatval($_POST['valor_compra']);
    $valor_venda = floatval($_POST['valor_venda']);
    $qtde = intval($_POST['qtde']);
    $categoria = $_POST['categoria'];
    $promocao = intval($_POST['promocao']);
    
    // Validações
    if (empty($nome_produto) || $valor_compra < 0 || $valor_venda < 0 || $qtde < 0) {
        $erro = "Por favor, preencha todos os campos corretamente.";
    } elseif (!in_array($categoria, ['Doces', 'Salgados', 'Bebidas', 'Combos', 'Promoção da Semana'])) {
        $erro = "Categoria inválida.";
    } elseif (!in_array($promocao, [0, 10, 20, 30, 40, 50])) {
        $erro = "Percentual de promoção inválido.";
    } elseif (!isset($_FILES['imagem']) || $_FILES['imagem']['error'] == UPLOAD_ERR_NO_FILE) {
        $erro = "A imagem do produto é obrigatória.";
    } else {
        // Processa a imagem
        $imagem = $_FILES['imagem'];
        $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        $extensao = strtolower(pathinfo($imagem['name'], PATHINFO_EXTENSION));

        // Verifica erros de upload
        if ($imagem['error'] != UPLOAD_ERR_OK) {
            switch ($imagem['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $erro = "A imagem excede o tamanho máximo permitido.";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $erro = "O upload da imagem foi interrompido.";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $erro = "Diretório temporário não encontrado.";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $erro = "Falha ao gravar a imagem no disco.";
                    break;
                default:
                    $erro = "Erro desconhecido ao fazer upload da imagem.";
                    break;
            }
        } elseif (!in_array($extensao, $extensoes_permitidas)) {
            $erro = "Formato de imagem inválido. Use JPG, PNG ou GIF.";
        } elseif ($imagem['size'] > 5 * 1024 * 1024) { // Limite de 5MB
            $erro = "A imagem excede o tamanho máximo de 5MB.";
        } else {
            $nome_arquivo = $upload_path . uniqid() . '.' . $extensao;
            $caminho_completo = $upload_dir . basename($nome_arquivo);
            
            if (!move_uploaded_file($imagem['tmp_name'], $caminho_completo)) {
                $erro = "Erro ao salvar a imagem. Verifique as permissões da pasta uploads.";
            }
        }
        
        if (!$erro) {
            // Calcula valor promocional
            $valor_promocional = $promocao > 0 ? $valor_venda * (1 - $promocao / 100) : null;
            
            // Insere no banco
            $sql = "INSERT INTO produto (nome_produto, valor_compra, valor_venda, qtde, imagem, categoria, promocao, valor_promocional) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sddisssd", $nome_produto, $valor_compra, $valor_venda, $qtde, $nome_arquivo, $categoria, $promocao, $valor_promocional);
            
            if ($stmt->execute()) {
                $sucesso = "Produto adicionado com sucesso!";
                header("Refresh: 2; url=painel.php");
            } else {
                $erro = "Erro ao adicionar produto: " . $mysqli->error;
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Novo Produto - AlimentaSESI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f7fff3;
            margin: 0;
            padding: 0;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .navbar {
            background-color: #ff7f2a;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .navbar a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            margin: 0 15px;
            transition: color 0.3s;
        }
        .navbar a:hover {
            color: #ffe6cc;
        }
        .container {
            max-width: 500px;
            margin: 30px auto;
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
            flex-grow: 1;
        }
        h2 {
            color: #ff7f2a;
            margin-bottom: 20px;
            font-size: 1.8em;
        }
        label {
            display: block;
            text-align: left;
            margin: 10px 0 5px;
            font-weight: bold;
            color: #444;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
            box-sizing: border-box;
        }
        input[type="file"] {
            padding: 3px;
        }
        input:focus, select:focus {
            border-color: #ff7f2a;
            outline: none;
        }
        button {
            background-color: #ff7f2a;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            width: 100%;
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.1s;
        }
        button:hover {
            background-color: #e66f24;
            transform: scale(1.02);
        }
        button:active {
            transform: scale(0.98);
        }
        .back-link {
            display: block;
            margin-top: 15px;
            color: #ff7f2a;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }
        .back-link:hover {
            color: #e66f24;
        }
        .mensagem-sucesso {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 0.9em;
        }
        .mensagem-erro {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 0.9em;
        }
        @media (max-width: 600px) {
            .container {
                max-width: 90%;
                margin: 20px auto;
                padding: 15px;
            }
            h2 {
                font-size: 1.5em;
            }
            .navbar {
                flex-direction: column;
                gap: 10px;
            }
            .navbar a {
                margin: 5px 0;
            }
        }
        footer {
    background-color: #ff7f2a;
    color: white;
    padding: 15px 0;
    text-align: center;
    width: 100%;
    /* Remova position: fixed, bottom e left */
    z-index: 1000; /* Mantém o footer acima de outros elementos, se necessário */
        }

        .footer-content p {
            margin: 5px 0;
            font-size: 1em;
        }

        .footer-content p a {
            color: #ffe6cc;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-content p a:hover {
            color: #ffffff;
            text-decoration: underline;
        }

        .footer-content p a i {
            margin-right: 5px;
        }

        body {
            margin: 0; /* Remove margens padrão */
            /* Remova padding-bottom, pois não é necessário sem position: fixed */
        }

        /* Garante que o footer fique no final da página */
        html, body {
            height: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Garante que o body ocupe pelo menos a altura total da janela */
        }

        main {
            flex: 1 0 auto; /* Faz o conteúdo principal ocupar o espaço disponível */
        }

        footer {
            flex-shrink: 0; /* Impede que o footer encolha */
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="links">
            <a href="paineldeacesso.php"><i class="fas fa-arrow-left"></i> Voltar ao Menu</a>
        </div>
        <div class="links">
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
        </div>
    </div>
    <div class="container">
        <h2><i class="fas fa-box-open"></i> Adicionar Novo Produto</h2>
        <?php if ($sucesso): ?>
            <div class="mensagem-sucesso"><?php echo htmlspecialchars($sucesso); ?></div>
        <?php endif; ?>
        <?php if ($erro): ?>
            <div class="mensagem-erro"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="nome_produto">Nome do Produto:</label>
            <input type="text" id="nome_produto" name="nome_produto" value="<?php echo isset($_POST['nome_produto']) ? htmlspecialchars($_POST['nome_produto']) : ''; ?>" required>

            <label for="valor_compra">Valor de Compra:</label>
            <input type="number" step="0.01" min="0" id="valor_compra" name="valor_compra" value="<?php echo isset($_POST['valor_compra']) ? htmlspecialchars($_POST['valor_compra']) : ''; ?>" required>

            <label for="valor_venda">Valor de Venda:</label>
            <input type="number" step="0.01" min="0" id="valor_venda" name="valor_venda" value="<?php echo isset($_POST['valor_venda']) ? htmlspecialchars($_POST['valor_venda']) : ''; ?>" required>

            <label for="qtde">Quantidade:</label>
            <input type="number" min="0" id="qtde" name="qtde" value="<?php echo isset($_POST['qtde']) ? htmlspecialchars($_POST['qtde']) : ''; ?>" required>

            <label for="categoria">Categoria:</label>
            <select id="categoria" name="categoria" required>
                <option value="Doces" <?php echo isset($_POST['categoria']) && $_POST['categoria'] == 'Doces' ? 'selected' : ''; ?>>Doces</option>
                <option value="Salgados" <?php echo isset($_POST['categoria']) && $_POST['categoria'] == 'Salgados' ? 'selected' : ''; ?>>Salgados</option>
                <option value="Bebidas" <?php echo isset($_POST['categoria']) && $_POST['categoria'] == 'Bebidas' ? 'selected' : ''; ?>>Bebidas</option>
                <option value="Combos" <?php echo isset($_POST['categoria']) && $_POST['categoria'] == 'Combos' ? 'selected' : ''; ?>>Combos</option>
                <option value="Promoção da Semana" <?php echo isset($_POST['categoria']) && $_POST['categoria'] == 'Promoção da Semana' ? 'selected' : ''; ?>>Promoção da Semana</option>
            </select>

            <label for="promocao">Promoção:</label>
            <select id="promocao" name="promocao">
                <option value="0" <?php echo isset($_POST['promocao']) && $_POST['promocao'] == 0 ? 'selected' : ''; ?>>Sem Promoção</option>
                <option value="10" <?php echo isset($_POST['promocao']) && $_POST['promocao'] == 10 ? 'selected' : ''; ?>>10% OFF</option>
                <option value="20" <?php echo isset($_POST['promocao']) && $_POST['promocao'] == 20 ? 'selected' : ''; ?>>20% OFF</option>
                <option value="30" <?php echo isset($_POST['promocao']) && $_POST['promocao'] == 30 ? 'selected' : ''; ?>>30% OFF</option>
                <option value="40" <?php echo isset($_POST['promocao']) && $_POST['promocao'] == 40 ? 'selected' : ''; ?>>40% OFF</option>
                <option value="50" <?php echo isset($_POST['promocao']) && $_POST['promocao'] == 50 ? 'selected' : ''; ?>>50% OFF</option>
            </select>

            <label for="imagem">Imagem do Produto:</label>
            <input type="file" id="imagem" name="imagem" accept="image/jpeg,image/png,image/gif" required>

            <button type="submit"><i class="fas fa-plus"></i> Adicionar Produto</button>
        </form>
        <a href="painel.php" class="back-link"><i class="fas fa-arrow-left"></i> Voltar ao Estoque</a>
    </div>

    <footer>
        <div class="footer-content">
            <p>Todos os direitos reservados © <?php echo date('Y'); ?></p>
            <p>Contato: <a href="https://wa.me/+5547984617515" target="_blank"><i class="fab fa-whatsapp"></i> (47)984617515</a></p>
        </div>
    </footer>

</body>
</html>