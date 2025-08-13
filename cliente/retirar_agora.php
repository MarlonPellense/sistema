<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['id_cliente']) || !isset($_GET['pedido'])) {
    header("Location: index.php");
    exit;
}

$id_cliente = $_SESSION['id_cliente'];
$id_pedido = intval($_GET['pedido']);

// Verifica se o pedido pertence ao cliente e está pendente
$stmt = $mysqli->prepare("SELECT id_pedido FROM pedidos WHERE id_pedido = ? AND id_cliente = ? AND status = 'novo'");
$stmt->bind_param("ii", $id_pedido, $id_cliente);
$stmt->execute();
$result = $stmt->get_result();
if (!$result || $result->num_rows == 0) {
    header("Location: painelcliente.php");
    exit;
}
$stmt->close();

// Processa a retirada
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_retirada'])) {
    $stmt = $mysqli->prepare("UPDATE pedidos SET status = 'pago' WHERE id_pedido = ? AND id_cliente = ?");
    $stmt->bind_param("ii", $id_pedido, $id_cliente);
    $stmt->execute();
    $stmt->close();
    // Limpa ultimo_pedido_id
    unset($_SESSION['ultimo_pedido_id']);
    header("Location: painelcliente.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retirar Pedido</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            padding: 30px;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-image: url("imagens/fundo_site1.png");
            background-size: cover;
            background-position: center;
            background-repeat: repeat;
        
        }
        .card-retirada {
            background-color: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            max-width: 500px;
            text-align: center;
        }
        .card-retirada h1 {
            color: #ff7f2a;
            margin-bottom: 20px;
        }
        .card-retirada p {
            font-size: 1.2em;
            margin: 10px 0;
        }
        .card-retirada button {
            padding: 10px 20px;
            font-size: 1em;
            background-color: #ff7f2a;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 20px;
        }
        .card-retirada button:hover {
            background-color: #e66c1f;
        }
        @media (max-width: 600px) {
            body {
                padding: 15px;
            }
            .card-retirada {
                padding: 15px;
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

<div class="card-retirada">
    <h1>Retirada do Pedido</h1>
    <p><strong>Número do Pedido:</strong> <?= htmlspecialchars($id_pedido) ?></p>
    <p>Apresente esse card no balcão da cantina e faça a retirada do seu lanche.</p>
    <form method="POST">
        <button type="submit" name="confirmar_retirada">Pedido Retirado</button>
    </form>
</div>

</body>
</html>