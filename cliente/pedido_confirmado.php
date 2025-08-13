<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['id_cliente']) || !isset($_SESSION['ultimo_pedido_id'])) {
    header("Location: index.php");
    exit;
}

$id_cliente = $_SESSION['id_cliente'];
$id_pedido = $_SESSION['ultimo_pedido_id'];

// Consulta o nome do cliente
$stmt_cliente = $mysqli->prepare("SELECT nome_cliente FROM cliente WHERE id_cliente = ?");
$stmt_cliente->bind_param("i", $id_cliente);
$stmt_cliente->execute();
$result_cliente = $stmt_cliente->get_result();
$nome_cliente = ($result_cliente && $result_cliente->num_rows > 0) 
    ? $result_cliente->fetch_assoc()['nome_cliente'] 
    : "Cliente não encontrado";
$stmt_cliente->close();

// Consulta detalhes do pedido
$stmt_pedido = $mysqli->prepare("SELECT unidade, data_pedido, forma_pagamento, total FROM pedidos WHERE id_pedido = ? AND id_cliente = ?");
$stmt_pedido->bind_param("ii", $id_pedido, $id_cliente);
$stmt_pedido->execute();
$result_pedido = $stmt_pedido->get_result();
if ($result_pedido && $result_pedido->num_rows > 0) {
    $pedido = $result_pedido->fetch_assoc();
    $unidade = $pedido['unidade'] ?? "---";
    $data_pedido = date('d/m/Y H:i:s', strtotime($pedido['data_pedido']));
    $forma_pagamento = $pedido['forma_pagamento'] ?? "---";
    $total = $pedido['total'] ?? 0;
} else {
    $unidade = "---";
    $data_pedido = date('d/m/Y H:i:s');
    $forma_pagamento = $_SESSION['forma_pagamento'] ?? "---";
    $total = $_SESSION['ultimo_total'] ?? 0;
}
$stmt_pedido->close();

// Limpa variáveis temporárias da sessão, mas mantém id_pedido
unset($_SESSION['ultimo_total'], $_SESSION['forma_pagamento']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Confirmado</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            padding: 30px;
            color: #333;
            margin: 0;
            background-image: url("imagens/fundo_site1.png");
            background-size: cover;
            background-position: center;
            background-repeat: repeat;
           
        }
        .nota-fiscal {
            background-color: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: auto;
        }
        .nota-fiscal h1 {
            color: #ff7f2a;
            text-align: center;
        }
        .nota-fiscal p {
            margin: 10px 0;
            font-size: 1.1em;
        }
        .nota-fiscal strong {
            color: #444;
        }
        .botoes {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }
        .botoes button {
            padding: 10px 20px;
            font-size: 1em;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s;
            color: white;
        }
        .botoes .retirar-agora {
            background-color: #ff7f2a;
        }
        .botoes .retirar-agora:hover {
            background-color: #e66c1f;
        }
        .botoes .retirar-depois {
            background-color: #6c757d;
        }
        .botoes .retirar-depois:hover {
            background-color: #5a6268;
        }
        @media (max-width: 600px) {
            body {
                padding: 15px;
            }
            .nota-fiscal {
                padding: 15px;
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

<div class="nota-fiscal">
    <h1>✅ Pedido Confirmado!</h1>
    <p><strong>Cliente:</strong> <?= htmlspecialchars($nome_cliente) ?></p>
    <p><strong>ID do Pedido:</strong> <?= htmlspecialchars($id_pedido) ?></p>
    <p><strong>Unidade:</strong> <?= htmlspecialchars($unidade) ?></p>
    <p><strong>Forma de Pagamento:</strong> <?= htmlspecialchars($forma_pagamento) ?></p>
    <p><strong>Data e Hora do Pedido:</strong> <?= htmlspecialchars($data_pedido) ?></p>
    <p><strong>Total:</strong> R$ <?= number_format($total, 2, ',', '.') ?></p>
    <div class="botoes">
        <button class="retirar-agora" onclick="window.location.href='retirar_agora.php?pedido=<?= $id_pedido ?>'">Retirar Agora</button>
        <button class="retirar-depois" onclick="window.location.href='painelcliente.php'">Retirar Depois</button>
    </div>
</div>


</body>
</html>