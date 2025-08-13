<?php
session_start();
include('conexao.php');

// Verifica se o cliente está logado
if (!isset($_SESSION['id_cliente'])) {
    header("Location: index.php");
    exit;
}

$id_cliente = $_SESSION['id_cliente'];

// Inicializa o carrinho do cliente se não existir
if (!isset($_SESSION['carrinho'][$id_cliente])) {
    $_SESSION['carrinho'][$id_cliente] = [];
}

$carrinho = $_SESSION['carrinho'][$id_cliente];

if (isset($_GET['erro'])) {
    echo '<div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin: 10px auto; max-width: 600px; text-align: center;">' . htmlspecialchars($_GET['erro']) . '</div>';
}

if (isset($_GET['remover'])) {
    $id_remover = intval($_GET['remover']);
    if (isset($_SESSION['carrinho'][$id_cliente][$id_remover])) {
        unset($_SESSION['carrinho'][$id_cliente][$id_remover]);
    }
    header("Location: carrinho.php");
    exit;
}

$subpreco = 0;

// Total geral
$total = 0;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seu Carrinho</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
            color: #333;
        }
        h1 {
            color: #ff6f00;
            text-align: center;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .produto {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .produto button {
            background-color: #ff4e4e;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .produto button:hover {
            background-color: #e64444;
        }
        .total {
            font-weight: bold;
            font-size: 1.2em;
            text-align: right;
            margin: 20px 0;
        }
        .checkout {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .checkout label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .checkout select, .checkout input[type="text"], .checkout input[type="radio"] {
            margin-bottom: 10px;
            padding: 10px;
            width: 100%;
            max-width: 400px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .checkout button {
            background-color: #28a745;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            max-width: 400px;
            font-size: 1.1em;
            transition: background-color 0.3s;
        }
        .checkout button:hover {
            background-color: #218838;
        }
        .error-message {
            color: red;
            background-color: #ffe0e0;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .promo-tag {
            color: #28a745;
            font-weight: bold;
            font-size: 0.9em;
        }
        @media (max-width: 600px) {
            .produto {
                flex-direction: column;
                align-items: flex-start;
            }
            .produto button {
                margin-top: 10px;
            }
        }
        
    </style>
</head>
<body>
<div class="container">
    <?php if (isset($_GET['erro']) && $_GET['erro'] === 'unidade'): ?>
        <p class="error-message">⚠️ Por favor, selecione sua unidade antes de finalizar o pedido.</p>
    <?php endif; ?>

    <h1>Seu Carrinho</h1>

    <?php if (empty($carrinho)): ?>
        <p>Seu carrinho está vazio. <a href="painelcliente.php">Ver produtos</a></p>
    <?php else: ?>
        <?php foreach ($carrinho as $id_produto => $item): 
            $id_produto = intval($id_produto);
            $nome = htmlspecialchars($item['nome']);
            $quantidade = $item['quantidade'];
            $subpreco = $item['subpreco'];
            $preco = $item['preco'];
            $promocao = $item['promocao'];
            $subtotal = $preco * $quantidade;
            $total += $subtotal;
        ?>
            <div class="produto">
                <div>
                    <strong><?= $nome ?></strong><br>
                    Quantidade: <?= $quantidade ?><br>
                    Preço unitário: R$ <?= number_format($subpreco, 2, ',', '.') ?>
                    <?php if ($promocao > 0): ?>
                        <span class="promo-tag">(<?= $promocao ?>% OFF)</span>
                    <?php endif; ?><br>
                    Subtotal: R$ <?= number_format($subtotal, 2, ',', '.') ?>
                </div>
                <a href="carrinho.php?remover=<?= $id_produto ?>">
                    <button>Remover</button>
                </a>
            </div>
        <?php endforeach; ?>

        <p class="total">Total: R$ <?= number_format($total, 2, ',', '.') ?></p>

        <!-- FORMULÁRIO DE FINALIZAÇÃO -->
        <div class="checkout">
            <form id="checkout-form" action="processa_pedido.php" method="POST">
                <label for="unidade">Selecione sua unidade:</label>
                <select name="unidade" id="unidade" required>
                    <option value="">-- Escolha a unidade --</option>
                    <option value="Unidade Centro">Unidade Centro</option>
                    <option value="Unidade Norte">Unidade Norte</option>
                    <option value="Unidade Sul">Unidade Sul</option>
                    <option value="Unidade Leste">Unidade Leste</option>
                </select>

                <label>Escolha a forma de pagamento:</label>
                <input type="radio" name="pagamento" value="pix" id="pix" required> <label for="pix">Pix</label><br>
                <input type="radio" name="pagamento" value="cartao" id="cartao" required> <label for="cartao">Cartão de Crédito/Débito</label>

                <div id="cartao-dados" style="display: none;">
                    <h3>Formulário de Pagamento com Cartão</h3>
                    <label for="nome_cartao">Nome no Cartão:</label>
                    <input type="text" name="nome_cartao" id="nome_cartao"><br>

                    <label for="numero_cartao">Número do Cartão:</label>
                    <input type="text" name="numero_cartao" id="numero_cartao"><br>

                    <label for="validade_cartao">Validade (MM/AA):</label>
                    <input type="text" name="validade_cartao" id="validade_cartao"><br>

                    <label for="cvv_cartao">CVV:</label>
                    <input type="text" name="cvv_cartao" id="cvv_cartao"><br>
                </div>

                <button type="submit">Finalizar Pedido</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<script>
    // Mostrar/esconder dados do cartão
    const cartaoRadio = document.getElementById('cartao');
    const pixRadio = document.getElementById('pix');
    const cartaoDados = document.getElementById('cartao-dados');

    cartaoRadio.addEventListener('change', () => {
        cartaoDados.style.display = 'block';
        document.getElementById('nome_cartao').required = true;
        document.getElementById('numero_cartao').required = true;
        document.getElementById('validade_cartao').required = true;
        document.getElementById('cvv_cartao').required = true;
    });

    pixRadio.addEventListener('change', () => {
        cartaoDados.style.display = 'none';
        document.getElementById('nome_cartao').required = false;
        document.getElementById('numero_cartao').required = false;
        document.getElementById('validade_cartao').required = false;
        document.getElementById('cvv_cartao').required = false;
    });

    // Validação do formulário antes do envio
    document.getElementById('checkout-form').addEventListener('submit', (e) => {
        const unidade = document.getElementById('unidade').value;
        if (!unidade) {
            e.preventDefault();
            window.location.href = 'carrinho.php?erro=unidade';
        }
    });
</script>

</body>
</html>