<?php
require_once 'conexao.php'; // Conexão com o banco
require_once 'protect.php'; // Proteção de acesso

if (!isset($mysqli)) {
    header("Location: painel.php?erro=Conexão com o banco falhou");
    exit;
}

$erro = '';
$sucesso = '';

if (isset($_GET['produto_id'])) {
    $produto_id = intval($_GET['produto_id']);
    
    $sql = "SELECT * FROM produto WHERE id_produto = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $produto_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $produto = $result->fetch_assoc();
    } else {
        header("Location: painel.php?erro=Produto não encontrado");
        exit;
    }
    $stmt->close();
} else {
    header("Location: painel.php?erro=ID do produto não fornecido");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
    } else {
        // Calcula valor promocional
        $valor_promocional = $promocao > 0 ? $valor_venda * (1 - $promocao / 100) : null;
        
        // Processa a imagem
        $imagem = $produto['imagem'];
        if (isset($_FILES['imagem']) && $_FILES['imagem']['name']) {
            $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
            $extensao = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
            if (!in_array($extensao, $extensoes_permitidas)) {
                $erro = "Formato de imagem inválido. Use JPG, PNG ou GIF.";
            } else {
                $nome_arquivo = 'uploads/' . uniqid() . '.' . $extensao;
                if (move_uploaded_file($_FILES['imagem']['tmp_name'], $nome_arquivo)) {
                    $imagem = $nome_arquivo;
                } else {
                    $erro = "Erro ao fazer upload da imagem.";
                }
            }
        }
        
        if (!$erro) {
            $sql = "UPDATE produto SET nome_produto = ?, valor_compra = ?, valor_venda = ?, qtde = ?, imagem = ?, categoria = ?, promocao = ?, valor_promocional = ? WHERE id_produto = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sddisssdi", $nome_produto, $valor_compra, $valor_venda, $qtde, $imagem, $categoria, $promocao, $valor_promocional, $produto_id);
            
            if ($stmt->execute()) {
                $sucesso = "Produto atualizado com sucesso!";
                header("Refresh: 2; url=painel.php");
            } else {
                $erro = "Erro ao atualizar produto. Tente novamente.";
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
    <title>Editar Produto - AlimentaSESI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f7fff3;
            margin: 0;
            padding: 20px;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            max-width: 450px;
            width: 100%;
            text-align: center;
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
        input:focus, select:focus {
            border-color: #ff7f2a;
            outline: none;
        }
        img {
            max-width: 150px;
            margin: 10px 0;
            border-radius: 5px;
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
                padding: 15px;
                margin: 10px;
            }
            h2 {
                font-size: 1.5em;
            }
            button {
                padding: 10px;
            }
        }

        footer {
    background-color: #ff7f2a;
    color: white;
    padding: 15px 0;
    text-align: center;
    width: 100%;
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
            margin: 0; 
        }

        html, body {
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1 0 auto; /* Faz o conteúdo principal ocupar o espaço disponível */
        }

        footer {
            flex-shrink: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-edit"></i> Editar Produto</h2>
        <?php if ($sucesso) { ?>
            <div class="mensagem-sucesso"><?php echo htmlspecialchars($sucesso); ?></div>
        <?php } ?>
        <?php if ($erro) { ?>
            <div class="mensagem-erro"><?php echo htmlspecialchars($erro); ?></div>
        <?php } ?>
        <form method="POST" enctype="multipart/form-data">
            <label>Nome do Produto:</label>
            <input type="text" name="nome_produto" value="<?php echo htmlspecialchars($produto['nome_produto']); ?>" required>
            
            <label>Valor de Compra:</label>
            <input type="number" step="0.01" min="0" name="valor_compra" value="<?php echo htmlspecialchars($produto['valor_compra']); ?>" required>
            
            <label>Valor de Venda:</label>
            <input type="number" step="0.01" min="0" name="valor_venda" value="<?php echo htmlspecialchars($produto['valor_venda']); ?>" required>
            
            <label>Quantidade em Estoque:</label>
            <input type="number" min="0" name="qtde" value="<?php echo htmlspecialchars($produto['qtde']); ?>" required>
            
            <label>Categoria:</label>
            <select name="categoria" required>
                <option value="Doces" <?php echo $produto['categoria'] == 'Doces' ? 'selected' : ''; ?>>Doces</option>
                <option value="Salgados" <?php echo $produto['categoria'] == 'Salgados' ? 'selected' : ''; ?>>Salgados</option>
                <option value="Bebidas" <?php echo $produto['categoria'] == 'Bebidas' ? 'selected' : ''; ?>>Bebidas</option>
                <option value="Combos" <?php echo $produto['categoria'] == 'Combos' ? 'selected' : ''; ?>>Combos</option>
                <option value="Promoção da Semana" <?php echo $produto['categoria'] == 'Promoção da Semana' ? 'selected' : ''; ?>>Promoção da Semana</option>
            </select>
            
            <label>Promoção:</label>
            <select name="promocao">
                <option value="0" <?php echo $produto['promocao'] == 0 ? 'selected' : ''; ?>>Sem Promoção</option>
                <option value="10" <?php echo $produto['promocao'] == 10 ? 'selected' : ''; ?>>10% OFF</option>
                <option value="20" <?php echo $produto['promocao'] == 20 ? 'selected' : ''; ?>>20% OFF</option>
                <option value="30" <?php echo $produto['promocao'] == 30 ? 'selected' : ''; ?>>30% OFF</option>
                <option value="40" <?php echo $produto['promocao'] == 40 ? 'selected' : ''; ?>>40% OFF</option>
                <option value="50" <?php echo $produto['promocao'] == 50 ? 'selected' : ''; ?>>50% OFF</option>
            </select>
            
            <label>Filtros Alimentares:</label>
            <div style="display: block; margin-right: 300px;">
                <input type="checkbox" name="glutten">
                <input type="checkbox" name="glutten">
                <input type="checkbox" name="glutten">
                <input type="checkbox" name="glutten">
            </div>

            <label>Imagem Atual:</label>
            <?php if ($produto['imagem']) { ?>
                <img src="<?php echo htmlspecialchars($produto['imagem']); ?>" alt="Imagem Atual">
            <?php } else { ?>
                <p>Sem imagem</p>
            <?php } ?>
            <label>Alterar Imagem:</label>
            <input type="file" name="imagem" accept="image/*">
            
            <button type="submit"><i class="fas fa-save"></i> Atualizar Produto</button>
        </form>
        <a href="painel.php" class="back-link"><i class="fas fa-arrow-left"></i> Voltar</a>
    </div>
                <br>
    <footer>
        <div class="footer-content">
            <p>Todos os direitos reservados © <?php echo date('Y'); ?></p>
            <p>Contato: <a href="https://wa.me/+5547984617515" target="_blank"><i class="fab fa-whatsapp"></i> (47)984617515</a></p>
        </div>
    </footer>

</body>
</html>