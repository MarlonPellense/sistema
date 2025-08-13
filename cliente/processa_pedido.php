<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['id_cliente']) || empty($_POST['unidade']) || empty($_POST['pagamento'])) {
    header("Location: carrinho.php?erro=unidade");
    exit;
}

$id_cliente = $_SESSION['id_cliente'];
$unidade = $_POST['unidade'];
$forma_pagamento = $_POST['pagamento'];
$carrinho = $_SESSION['carrinho'][$id_cliente] ?? [];

// Inicia transação
$mysqli->begin_transaction();

try {
    // Calcula o total e valida estoque
    $total = 0;
    $itens_validos = [];
    foreach ($carrinho as $id_produto => $item) {
        $id_produto = intval($id_produto);
        $quantidade = intval($item['quantidade']);
        $preco = floatval($item['preco']);
        if ($quantidade <= 0) {
            continue; // Ignora quantidades inválidas
        }
        $stmt = $mysqli->prepare("SELECT qtde FROM produto WHERE id_produto = ? FOR UPDATE");
        $stmt->bind_param("i", $id_produto);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $produto = $result->fetch_assoc();
            if ($produto['qtde'] < $quantidade) {
                throw new Exception("Estoque insuficiente para o produto ID $id_produto. Disponível: {$produto['qtde']}, solicitado: $quantidade.");
            }
            $subtotal = $preco * $quantidade;
            $total += $subtotal;
            $itens_validos[] = [
                'id_produto' => $id_produto,
                'quantidade' => $quantidade,
                'valor_venda' => $preco
            ];
        } else {
            throw new Exception("Produto ID $id_produto não encontrado.");
        }
        $stmt->close();
    }

    if ($total <= 0 || empty($itens_validos)) {
        throw new Exception("Carrinho vazio ou inválido.");
    }

    // Insere o pedido no banco
    $stmt = $mysqli->prepare("INSERT INTO pedidos (id_cliente, unidade, forma_pagamento, total, data_pedido, status) VALUES (?, ?, ?, ?, NOW(), 'novo')");
    $stmt->bind_param("issd", $id_cliente, $unidade, $forma_pagamento, $total);
    $stmt->execute();
    $ultimo_pedido_id = $mysqli->insert_id;
    $stmt->close();

    // Insere itens do pedido na tabela vendas e atualiza estoque
    foreach ($itens_validos as $item) {
        // Insere em vendas
        $stmt = $mysqli->prepare("INSERT INTO vendas (produto_id_produto, valor_venda, data_venda, quantidade_vendida, id_pedido, id_cliente, forma_pagamento) VALUES (?, ?, NOW(), ?, ?, ?, ?)");
        $stmt->bind_param("idiiis", $item['id_produto'], $item['valor_venda'], $item['quantidade'], $ultimo_pedido_id, $id_cliente, $forma_pagamento);
        $stmt->execute();
        $stmt->close();

        // Atualiza estoque
        $stmt = $mysqli->prepare("UPDATE produto SET qtde = qtde - ? WHERE id_produto = ?");
        $stmt->bind_param("ii", $item['quantidade'], $item['id_produto']);
        $stmt->execute();
        $stmt->close();
    }

    // Confirma transação
    $mysqli->commit();

    // Armazena na sessão
    $_SESSION['ultimo_pedido_id'] = $ultimo_pedido_id;
    $_SESSION['ultimo_total'] = $total;
    $_SESSION['forma_pagamento'] = $forma_pagamento;

    // Limpa o carrinho
    unset($_SESSION['carrinho'][$id_cliente]);

    // Redireciona para confirmação
    header("Location: pedido_confirmado.php");
    exit;
} catch (Exception $e) {
    // Reverte transação em caso de erro
    $mysqli->rollback();
    // Redireciona com mensagem de erro
    header("Location: carrinho.php?erro=" . urlencode($e->getMessage()));
    exit;
}
?>