<?php
require_once 'protect.php'; // Proteção de acesso
require_once 'conexao.php'; // Conexão com o banco

$sucesso = '';
$erro = '';
$ano_atual = date('Y'); // Obtém o ano atual
$mes_atual = date('n'); // Obtém o mês atual (sem zero à esquerda)

$ano_selecionado = isset($_GET['ano']) ? (int)$_GET['ano'] : $ano_atual;
$mes_selecionado = isset($_GET['mes']) ? (int)$_GET['mes'] : $mes_atual;

$produtos = [];
$quantidades = [];

try {
    $check_query = "SELECT COUNT(*) FROM relatorios WHERE ano = ? AND mes = ?";
    $stmt = $mysqli->prepare($check_query);
    $stmt->bind_param("ii", $ano_atual, $mes_atual);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count == 0) {
        $insert_query = "
            INSERT INTO relatorios (ano, mes, vendidos_mes, total_vendas, produto_mais_vendido)
            SELECT ?, ?,
                (SELECT COUNT(*) FROM vendas v WHERE MONTH(v.data_venda) = ? AND YEAR(v.data_venda) = ?),
                (SELECT COALESCE(SUM(v.valor_venda * v.quantidade_vendida), 0) FROM vendas v WHERE MONTH(v.data_venda) = ? AND YEAR(v.data_venda) = ?),
                (SELECT COALESCE(
                    (SELECT p.nome_produto 
                     FROM vendas v2 
                     JOIN produto p ON v2.produto_id_produto = p.id_produto
                     WHERE MONTH(v2.data_venda) = ? 
                       AND YEAR(v2.data_venda) = ?
                     GROUP BY p.id_produto 
                     ORDER BY SUM(v2.valor_venda * v2.quantidade_vendida) DESC 
                     LIMIT 1), 'Nenhum'
                ))
        ";
        $stmt = $mysqli->prepare($insert_query);
        $stmt->bind_param("iiiiiiii", $ano_atual, $mes_atual, $mes_atual, $ano_atual, $mes_atual, $ano_atual, $mes_atual, $ano_atual);
        $stmt->execute();
        $stmt->close();
    } else {
        $update_query = "
            UPDATE relatorios
            SET 
                vendidos_mes = (SELECT COUNT(*) FROM vendas v WHERE MONTH(v.data_venda) = ? AND YEAR(v.data_venda) = ?),
                total_vendas = (SELECT COALESCE(SUM(v.valor_venda * v.quantidade_vendida), 0) FROM vendas v WHERE MONTH(v.data_venda) = ? AND YEAR(v.data_venda) = ?),
                produto_mais_vendido = (SELECT COALESCE(
                    (SELECT p.nome_produto 
                     FROM vendas v2 
                     JOIN produto p ON v2.produto_id_produto = p.id_produto
                     WHERE MONTH(v2.data_venda) = ? 
                       AND YEAR(v2.data_venda) = ?
                     GROUP BY p.id_produto 
                     ORDER BY SUM(v2.valor_venda * v2.quantidade_vendida) DESC 
                     LIMIT 1), 'Nenhum'
                ))
            WHERE ano = ? AND mes = ?
        ";
        $stmt = $mysqli->prepare($update_query);
        $stmt->bind_param("iiiiiiii", $mes_atual, $ano_atual, $mes_atual, $ano_atual, $mes_atual, $ano_atual, $ano_atual, $mes_atual);
        $stmt->execute();
        $stmt->close();
    }

} catch (Exception $e) {
    // Em caso de erro, desfaz a transação
    $mysqli->rollback();
    echo "Erro: " . $e->getMessage();
}

$grafico_query = "SELECT p.nome_produto, SUM(v.quantidade_vendida) as total_vendido
                FROM vendas v
                JOIN produto p ON v.produto_id_produto = p.id_produto
                WHERE YEAR(v.data_venda) = $ano_selecionado 
                AND MONTH(v.data_venda) = $mes_selecionado
                GROUP BY p.id_produto
                ORDER BY total_vendido DESC";
$grafico_result = $mysqli->query($grafico_query);
$total_outros = 0;
$count = 0;

while ($row = $grafico_result->fetch_assoc()) {
    if ($count < 5) {
        $produtos[] = $row['nome_produto'];
        $quantidades[] = $row['total_vendido'];
    } else {
        $total_outros += $row['total_vendido'];
    }
    $count++;
}
if ($total_outros > 0) {
    $produtos[] = 'Outros';
    $quantidades[] = $total_outros;
}

$sql = "SELECT mes, ano FROM relatorios ORDER BY mes desc";
$resultado = $mysqli->query($sql);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios de Vendas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            text-align: center;
        }
        h2 {
            color: #ff7f2a;
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
        .filtros select {
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
        .grafico-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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
        @media (max-width: 600px) {
            .container {
                margin: 20px 10px;
            }
            .navbar {
                flex-direction: column;
                gap: 10px;
            }
            .grafico-container {
                max-width: 100%;
                padding: 10px;
            }
        }
        footer {
            background-color: #ff7f2a;
            color: white;
            padding: 15px 0;
            text-align: center;
            width: 100%;
            z-index: 1000;
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
            height: 100%;
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main {
            flex: 1 0 auto;
        }
        footer {
            flex-shrink: 0;
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
        <h2><i class="fas fa-chart-pie"></i> Relatórios de Vendas</h2>
        
        <?php if ($sucesso) { ?>
            <div class="mensagem-sucesso"><?php echo htmlspecialchars($sucesso); ?></div>
        <?php } ?>
        <?php if ($erro) { ?>
            <div class="mensagem-erro"><?php echo htmlspecialchars($erro); ?></div>
        <?php } ?>

        <div class="filtros">
            <form method="GET">
                <select name="mes" id="mes-select">
                    <option value="0/2000"></option>
                    <?php 
                    if ($resultado->num_rows > 0) {
                        while($row = $resultado->fetch_assoc()) {
                            $valor = $row["mes"] . "/" . $row["ano"];
                            $selected = ($row["mes"] == $mes_selecionado && $row["ano"] == $ano_selecionado) ? "selected" : "";
                            echo "<option value='$valor' $selected>" . $valor . "</option>";
                        }
                    } else {
                        echo "<option value=''>Nenhuma opção disponível</option>";
                    }
                    ?>
                </select>
            </form>
            <a href="http://localhost:8080/sistema/gerar_pdf.php" target="_blank">
                <button><i class="fas fa-file-pdf"></i> Gerar Relatório</button>
            </a>
        </div>

        <div class="grafico-container">
            <canvas id="vendasChart"></canvas>
        </div>
    </div>

    <script>
        var meses = ["", "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
        const ctx = document.getElementById('vendasChart').getContext('2d');
        var produtos = <?php echo json_encode($produtos); ?>;
        var quantidades = <?php echo json_encode($quantidades); ?>;
        var mes_selecionado = <?php echo json_encode($mes_selecionado); ?>;

        document.getElementById('mes-select').addEventListener('change', function() {
            var valor = this.value;
            var [mes, ano] = valor.split('/');
            window.location.href = `relatorio.php?mes=${mes}&ano=${ano}`;
        });

        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: produtos.length ? produtos : ['Nenhum dado'],
                datasets: [{
                    data: quantidades.length ? quantidades : [1],
                    backgroundColor: ['#ff7f2a', '#28a745', '#007bff', '#dc3545', '#e66f24', '#6c757d'],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 20,
                            padding: 15
                        }
                    },
                    title: {
                        display: true,
                        text: 'Top 5 Produtos - ' + meses[mes_selecionado],
                        color: '#444',
                        font: {
                            size: 16
                        }
                    }
                }
            }
        });
    </script>

    <footer>
        <div class="footer-content">
            <p>Todos os direitos reservados © <?php echo date('Y'); ?></p>
            <p>Contato: <a href="https://wa.me/+5547984617515" target="_blank"><i class="fab fa-whatsapp"></i> (47)984617515</a></p>
        </div>
    </footer>

</body>
</html>

<?php
$mysqli->close();
?>