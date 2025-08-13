<?php
require_once 'conexao.php';

$relatorios_query = "
    SELECT id_relatorio, mes, ano, vendidos_mes, total_vendas, produto_mais_vendido 
    FROM relatorios 
    WHERE ano = 2025
    GROUP BY mes, ano
    ORDER BY mes DESC";
$relatorios_result = $mysqli->query($relatorios_query);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Vendas</title>
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
            text-align: center;
        }
        h2 {
            color: #ff7f2a;
            margin-bottom: 20px;
            font-size: 1.8em;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #ff7f2a;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .no-print {
            margin: 20px 0;
        }
        .no-print a {
            background-color: #ff7f2a;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .no-print a:hover {
            background-color: #e66f24;
        }
        @media print {
            .navbar, .no-print {
                display: none;
            }
            .container {
                margin: 0;
                padding: 0;
            }
            table {
                box-shadow: none;
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
            th, td {
                padding: 8px;
                font-size: 0.9em;
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
            min-height: 100vh;        }

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
        <h2><i class="fas fa-file-alt"></i> Relatório de Vendas</h2>
        
        <div class="no-print">
            <a href="relatorio.php"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID Relatório</th>
                    <th>Mês/Ano</th>
                    <th>Vendidos</th>
                    <th>Total (R$)</th>
                    <th>Produto Mais Vendido</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($relatorios_result->num_rows > 0): ?>
                    <?php
                    $meses_nomes = [
                        1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril', 5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
                    ];
                    while ($relatorio = $relatorios_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($relatorio['id_relatorio']); ?></td>
                            <td><?php echo $meses_nomes[$relatorio['mes']] . ' ' . $relatorio['ano']; ?></td>
                            <td><?php echo $relatorio['vendidos_mes']; ?> itens</td>
                            <td>R$ <?php echo number_format($relatorio['total_vendas'], 2, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($relatorio['produto_mais_vendido']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Nenhum relatório encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>

<footer>
        <div class="footer-content">
            <p>Todos os direitos reservados © <?php echo date('Y'); ?></p>
            <p>Contato: <a href="https://wa.me/+5547984617515" target="_blank"><i class="fab fa-whatsapp"></i> (47)984617515</a></p>
        </div>
    </footer>

</body>
</html>