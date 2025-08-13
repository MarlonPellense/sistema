<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['id_cliente'])) {
    if (!isset($_POST['ajax'])) {
        header("Location: index.php");
        exit();
    } else {
        echo json_encode(["erro" => "Cliente não autenticado"]);
        exit();
    }
}

$id_cliente = $_SESSION['id_cliente'];
$id_produto = $_POST['id_produto'] ?? null;
$quantidade = $_POST['quantidade'] ?? 1;
$is_promo = isset($_POST['is_promo']) && $_POST['is_promo'] === 'true';

if (!$id_produto) {
    if (isset($_POST['ajax'])) {
        echo json_encode(["erro" => "ID do produto inválido"]);
        exit();
    } else {
        header("Location: painelcliente.php");
        exit();
    }
}

if (!isset($_SESSION['carrinho'][$id_cliente])) {
    $_SESSION['carrinho'][$id_cliente] = [];
}

$quantidade = max(1, min(10, intval($quantidade))); // Garante entre 1 e 10

// Consultar produto no banco
$stmt = $mysqli->prepare("SELECT nome_produto, valor_venda, promocao FROM produto WHERE id_produto = ?");
$stmt->bind_param("i", $id_produto);
$stmt->execute();
$result = $stmt->get_result();

if ($produto = $result->fetch_assoc()) {
    $preco = $produto['valor_venda'];
    $subpreco = $produto['valor_venda'];
    $promocao = $is_promo ? (int)$produto['promocao'] : 0;

    if ($promocao > 0) {
        $desconto = $promocao / 100;
        $preco = $preco * (1 - $desconto);
    }

    $_SESSION['carrinho'][$id_cliente][$id_produto] = [
        'nome' => $produto['nome_produto'],
        'quantidade' => $quantidade,
        'preco' => $preco,
        'subpreco' => $subpreco,
        'promocao' => $promocao
    ];
} else {
    if (isset($_POST['ajax'])) {
        echo json_encode(["erro" => "Produto não encontrado"]);
        exit();
    } else {
        header("Location: painelcliente.php");
        exit();
    }
}
$stmt->close();

// Se for AJAX, retorna JSON com número de tipos
if (isset($_POST['ajax'])) {
    echo json_encode([
        "sucesso" => true,
        "total_tipos" => count($_SESSION['carrinho'][$id_cliente])
    ]);
    exit();
} else {
    header("Location: painelcliente.php?adicionado=1");
    exit();
}
?>