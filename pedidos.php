<?php
require_once 'protect.php'; // Proteção de acesso
require_once 'conexao.php'; // Conexão com o banco

$erro = '';
$sucesso = '';
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$status_filtro = isset($_GET['status']) ? $_GET['status'] : '';

// Consulta para obter informações dos pedidos
$pedidos_query = "SELECT p.id_pedido, c.nome_cliente, p.data_pedido, p.status, p.total 
                  FROM pedidos p 
                  JOIN cliente c ON p.id_cliente = c.id_cliente 
                  WHERE (? = '' OR c.nome_cliente LIKE ?) 
                  AND (? = '' OR p.status = ?)
                  AND p.status != 'retirado'";
$stmt = $mysqli->prepare($pedidos_query);
$param_busca = "%$busca%";
$stmt->bind_param("ssss", $busca, $param_busca, $status_filtro, $status_filtro);
$stmt->execute();
$pedidos_result = $stmt->get_result();

// Função para atualizar o status do pedido
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $id_pedido = intval($_POST['id_pedido']);
    $novo_status = $_POST['update_status'];
    
    if ($novo_status === 'retirado') {
        $stmt = $mysqli->prepare("UPDATE pedidos SET status = ? WHERE id_pedido = ?");
        $stmt->bind_param("si", $novo_status, $id_pedido);
        if ($stmt->execute()) {
            $sucesso = "Pedido #$id_pedido marcado como retirado!";
            // Retorna JSON para JavaScript
            if (isset($_POST['ajax'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => $sucesso]);
                exit;
            }
        } else {
            $erro = "Erro ao atualizar o status do pedido #$id_pedido.";
            if (isset($_POST['ajax'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $erro]);
                exit;
            }
        }
        $stmt->close();
    } else {
        $erro = "Ação inválida.";
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Pedidos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f7fff3;
            margin: 0;
            padding: 0;
            color: #333;
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
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 15px;
        }
        h2 {
            color: #ff7f2a;
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.8em;
        }
        .filtros {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            justify-content: center;
        }
        .filtros input, .filtros select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
            width: 200px;
        }
        .filtros button {
            background-color: #ff7f2a;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .filtros button:hover {
            background-color: #e66f24;
        }
        .pedidos {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .pedido-card {
            background-color: white;
            width: 300px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 15px;
            transition: transform 0.3s;
        }
        .pedido-card:hover {
            transform: translateY(-5px);
        }
        .pedido-card h3 {
            margin: 0 0 10px;
            font-size: 1.2em;
            color: #444;
        }
        .pedido-card p {
            margin: 5px 0;
            font-size: 0.95em;
        }
        .status-novo {
            color: #007bff;
            font-weight: bold;
        }
        .status-pendente {
            color: #dc3545;
            font-weight: bold;
        }
        .status-pago {
            color: #28a745;
            font-weight: bold;
        }
        .status-retirado {
            color: #6c757d;
            font-weight: bold;
        }
        .detalhes {
            display: none;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
        .detalhes ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .detalhes li {
            margin: 5px 0;
            font-size: 0.9em;
        }
        .botoes {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        .botoes button {
            flex: 1;
            padding: 8px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .btn-retirado {
            background-color: #6c757d;
            color: white;
        }
        .btn-retirado:hover {
            background-color: #5a6268;
        }
        .btn-detalhes {
            background-color: #007bff;
            color: white;
            padding: 8px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            width: 100%;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .btn-detalhes:hover {
            background-color: #0056b3;
        }
        .mensagem-sucesso {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin: 15px auto;
            max-width: 600px;
            text-align: center;
        }
        .mensagem-erro {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin: 15px auto;
            max-width: 600px;
            text-align: center;
        }
        @media (max-width: 900px) {
            .pedidos {
                flex-direction: column;
                align-items: center;
            }
            .pedido-card {
                width: 100%;
                max-width: 350px;
            }
            .filtros {
                flex-direction: column;
                align-items: center;
            }
            .filtros input, .filtros select {
                width: 100%;
                max-width: 300px;
            }
        }
        @media (max-width: 600px) {
            .container {
                margin: 20px 10px;
            }
            .navbar {
                flex-direction: column;
                gap: 10px;
            }
            .botoes {
                flex-direction: column;
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
            margin: 0; /* Remove margens padrão */
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
        <h2><i class="fas fa-shopping-cart"></i> Gerenciar Pedidos</h2>
        
        <?php if ($sucesso && !isset($_POST['ajax'])) { ?>
            <div class="mensagem-sucesso"><?php echo htmlspecialchars($sucesso); ?></div>
        <?php } ?>
        <?php if ($erro && !isset($_POST['ajax'])) { ?>
            <div class="mensagem-erro"><?php echo htmlspecialchars($erro); ?></div>
        <?php } ?>

        <div class="filtros">
            <form method="get" style="display: flex; gap: 10px; flex-wrap: wrap;">
                <input type="text" name="busca" placeholder="Buscar por cliente..." value="<?php echo htmlspecialchars($busca); ?>">
                <select name="status" id="status" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    <option value="novo" <?= $status_filtro=='novo'?'selected':'' ?>>Novo</option>
                    <option value="pago" <?= $status_filtro=='pago'?'selected':'' ?>>Pago</option>
                </select>
                </form>
            <a href="relatorio.php" class="filtros"><button><i class="fas fa-chart-line"></i> Ir para Relatórios</button></a>
        </div>

        <div class="pedidos">
            <?php if ($pedidos_result->num_rows > 0) { ?>
                <?php while ($pedido = $pedidos_result->fetch_assoc()) { ?>
                    <div class="pedido-card" id="pedido-<?php echo $pedido['id_pedido']; ?>">
                        <h3>Pedido #<?php echo $pedido['id_pedido']; ?></h3>
                        <p><strong>Cliente:</strong> <?php echo htmlspecialchars($pedido['nome_cliente']); ?></p>
                        <p><strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></p>
                        <p><strong>Status:</strong> <span class="status-<?php echo strtolower($pedido['status']); ?>"><?php echo ucfirst($pedido['status']); ?></span></p>
                        <p><strong>Total:</strong> R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></p>
                        
                        <button class="btn-detalhes" onclick="toggleDetalhes('detalhes-<?php echo $pedido['id_pedido']; ?>')">
                            <i class="fas fa-list"></i> Ver Itens
                        </button>
                        
                        <div class="detalhes" id="detalhes-<?php echo $pedido['id_pedido']; ?>">
                            <?php
                            $detalhes_query = "SELECT pr.nome_produto, v.quantidade_vendida, v.valor_venda 
                                              FROM vendas v 
                                              JOIN produto pr ON v.produto_id_produto = pr.id_produto 
                                              WHERE v.id_pedido = ?";
                            $stmt_detalhes = $mysqli->prepare($detalhes_query);
                            $stmt_detalhes->bind_param("i", $pedido['id_pedido']);
                            $stmt_detalhes->execute();
                            $detalhes_result = $stmt_detalhes->get_result();
                            ?>
                            <ul>
                                <?php while ($item = $detalhes_result->fetch_assoc()) { ?>
                                    <li><?php echo htmlspecialchars($item['nome_produto']); ?> - <?php echo $item['quantidade_vendida']; ?> x R$ <?php echo number_format($item['valor_venda'], 2, ',', '.'); ?></li>
                                <?php } ?>
                            </ul>
                            <?php $stmt_detalhes->close(); ?>
                        </div>

                        <?php if ($pedido['status'] == 'novo' || $pedido['status'] == 'pago') { ?>
                            <div class="botoes">
                                <form method="POST" onsubmit="return marcarRetirado(event, <?php echo $pedido['id_pedido']; ?>)">
                                    <input type="hidden" name="id_pedido" value="<?php echo $pedido['id_pedido']; ?>">
                                    <input type="hidden" name="update_status" value="retirado">
                                    <input type="hidden" name="ajax" value="1">
                                    <button type="submit" class="btn-retirado">
                                        <i class="fas fa-check-circle"></i> Marcar como Retirado
                                    </button>
                                </form>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <p style="text-align: center; width: 100%;">Nenhum pedido encontrado.</p>
            <?php } ?>
        </div>
    </div>

    <script>
        function toggleDetalhes(id) {
            const detalhes = document.getElementById(id);
            detalhes.style.display = detalhes.style.display === 'block' ? 'none' : 'block';
        }

        function marcarRetirado(event, idPedido) {
            event.preventDefault();
            if (!confirm(`Tem certeza que deseja marcar o pedido #${idPedido} como retirado?`)) {
                return false;
            }

            const form = event.target;
            const formData = new FormData(form);

            fetch('pedidos.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const card = document.getElementById(`pedido-${idPedido}`);
                    card.style.transition = 'opacity 0.5s';
                    card.style.opacity = '0';
                    setTimeout(() => {
                        card.remove();
                        const mensagem = document.createElement('div');
                        mensagem.className = 'mensagem-sucesso';
                        mensagem.textContent = data.message;
                        document.querySelector('.container').insertBefore(mensagem, document.querySelector('.filtros'));
                        setTimeout(() => mensagem.remove(), 3000);
                    }, 500);
                } else {
                    const mensagem = document.createElement('div');
                    mensagem.className = 'mensagem-erro';
                    mensagem.textContent = data.message;
                    document.querySelector('.container').insertBefore(mensagem, document.querySelector('.filtros'));
                    setTimeout(() => mensagem.remove(), 3000);
                }
            })
            .catch(error => {
                const mensagem = document.createElement('div');
                mensagem.className = 'mensagem-erro';
                mensagem.textContent = 'Erro ao processar a ação.';
                document.querySelector('.container').insertBefore(mensagem, document.querySelector('.filtros'));
                setTimeout(() => mensagem.remove(), 3000);
            });

            return false;
        }
    </script>

<footer>
        <div class="footer-content">
            <p>Todos os direitos reservados © <?php echo date('Y'); ?></p>
            <p>Contato: <a href="https://wa.me/+5547984617515" target="_blank"><i class="fab fa-whatsapp"></i>(47)984617515</a></p>
        </div>
    </footer>

</body>
</html>