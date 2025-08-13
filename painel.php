<?php
require_once 'protect.php'; // Proteção de acesso
require_once 'conexao.php'; // Conexão com o banco

// Processa a pesquisa
$search = isset($_GET['search']) ? $mysqli->real_escape_string($_GET['search']) : '';
$categoria = isset($_GET['categoria']) ? $mysqli->real_escape_string($_GET['categoria']) : '';

$sql = "SELECT * FROM produto WHERE 1=1 AND stat = 'Ativo'";
if ($search) {
    $sql .= " AND nome_produto LIKE '%$search%'";
}
if ($categoria && in_array($categoria, ['Doces', 'Salgados', 'Bebidas', 'Combos', 'Promoção da Semana'])) {
    $sql .= " AND categoria = '$categoria'";
}
$result = $mysqli->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estoque - AlimentaSESI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f7fff3;
            margin: 0;
            padding: 0;
            color: #333;
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }
        .sidebar {
            width: 250px;
            background-color: #ff7f2a;
            color: white;
            padding: 20px;
            position: fixed;
            top: 0;
            bottom: 0;
            left: -250px;
            transition: left 0.3s ease;
            z-index: 1000;
            overflow-y: auto;
        }
        .sidebar.open {
            left: 0;
        }
        .sidebar h2 {
            font-size: 1.5em;
            margin-bottom: 20px;
            text-align: center;
        }
        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .sidebar a:hover {
            background-color: #e66f24;
        }
        .sidebar a.active {
            background-color: #e66f24;
            font-weight: bold;
        }
        .close-btn {
            background-color: transparent;
            color: white;
            border: none;
            font-size: 1.2em;
            cursor: pointer;
            position: absolute;
            top: 15px;
            right: 15px;
            transition: color 0.3s, transform 0.1s;
        }
        .close-btn:hover {
            color: #ffe6cc;
            transform: scale(1.1);
        }
        .close-btn:active {
            transform: scale(0.9);
        }
        .content {
            flex: 1;
            padding: 20px;
            transition: margin-left 0.3s ease;
            margin-left: 0;
        }
        .content.sidebar-open {
            margin-left: 250px;
        }
        
        .navbar {
            background-color: #ff7f2a;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: fixed; /* Fixa a navbar no topo */
            top: 0; /* Alinha ao topo da janela */
            left: 0; /* Alinha à esquerda */
            width: 100%; /* Garante que ocupe toda a largura */
            z-index: 1000; /* Garante que a navbar fique acima de outros elementos */
            box-sizing: border-box; /* Evita que o padding aumente a largura */
        }

        .navbar a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            margin: 0 15px;
            position: relative;
        }

        .navbar a:hover {
            color: #ffe6cc;
        }


        main {
            margin-top: 70px; 
            padding: 20px; 
        }
        .toggle-btn {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1100;
            background-color: #ff7f2a;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.1s;
        }
        .toggle-btn:hover {
            background-color: #e66f24;
            transform: scale(1.05);
        }
        .toggle-btn:active {
            transform: scale(0.95);
        }
        .toggle-btn.hidden {
            display: none;
        }
        .search-bar {
            margin: 20px auto;
            max-width: 500px;
            display: flex;
            align-items: center;
        }
        .search-bar input {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px 0 0 5px;
            font-size: 1em;
            outline: none;
            transition: border-color 0.3s;
        }
        .search-bar input:focus {
            border-color: #ff7f2a;
        }
        .search-bar button {
            padding: 10px 15px;
            background-color: #28a745;
            border: none;
            border-radius: 0 5px 5px 0;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .search-bar button:hover {
            background-color: #218838;
        }
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 15px;
        }
        h1 {
            text-align: center;
            color: #ff7f2a;
            margin-bottom: 20px;
        }
        .btn-adicionar {
            display: inline-block;
            padding: 10px 20px;
            background-color: #ff7f2a;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            margin: 15px auto;
            display: block;
            text-align: center;
            transition: background-color 0.3s, transform 0.1s;
        }
        .btn-adicionar:hover {
            background-color: #e66f24;
            transform: scale(1.02);
        }
        .btn-adicionar:active {
            transform: scale(0.98);
        }
        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .card {
            background-color: white;
            width: 280px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 15px;
            text-align: center;
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card img {
            width: 100%;
            max-height: 180px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .card h3 {
            font-size: 1.2em;
            margin: 10px 0 5px;
            color: #444;
        }
        .card p {
            margin: 5px 0;
            font-size: 1em;
            color: #555;
        }
        .card .promocao {
            color: #dc3545;
            font-weight: bold;
            margin: 5px 0;
        }
        .card .btn-editar, .card .btn-remover {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
            font-weight: bold;
            transition: background-color 0.3s, transform 0.1s;
        }
        .card .btn-editar {
            background-color: #ff7f2a;
            color: white;
        }
        .card .btn-editar:hover {
            background-color: #e66f24;
            transform: scale(1.02);
        }
        .card .btn-editar:active {
            transform: scale(0.98);
        }
        .card .btn-remover {
            background-color: #dc3545;
            color: white;
        }
        .card .btn-remover:hover {
            background-color: #c82333;
            transform: scale(1.02);
        }
        .card .btn-remover:active {
            transform: scale(0.98);
        }
        @media (max-width: 600px) {
            .navbar {
                flex-direction: column;
                gap: 10px;
            }
            .navbar a {
                margin: 5px 0;
            }
            .container {
                margin: 20px 10px;
            }
            .search-bar {
                flex-direction: column;
                gap: 10px;
            }
            .search-bar input {
                border-radius: 5px;
            }
            .search-bar button {
                border-radius: 5px;
                width: 100%;
            }
            .card {
                width: 100%;
                max-width: 300px;
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
    <button class="toggle-btn" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>
    <div class="sidebar" id="sidebar">
        <button class="close-btn" onclick="closeSidebar()">
            <i class="fas fa-times"></i>
        </button>
        <h2>Categorias</h2>
        <a href="?categoria=" class="<?php echo $categoria == '' ? 'active' : ''; ?>">Todos</a>
        <a href="?categoria=Doces" class="<?php echo $categoria == 'Doces' ? 'active' : ''; ?>">Doces</a>
        <a href="?categoria=Salgados" class="<?php echo $categoria == 'Salgados' ? 'active' : ''; ?>">Salgados</a>
        <a href="?categoria=Bebidas" class="<?php echo $categoria == 'Bebidas' ? 'active' : ''; ?>">Bebidas</a>
        <a href="?categoria=Combos" class="<?php echo $categoria == 'Combos' ? 'active' : ''; ?>">Combos</a>
        <a href="?categoria=Promoção da Semana" class="<?php echo $categoria == 'Promoção da Semana' ? 'active' : ''; ?>">Promoção da Semana</a>
    </div>

    <div class="content" id="content">
        <div class="navbar">
            <div class="links">
                <a href="paineldeacesso.php"><i class="fas fa-arrow-left"></i> Voltar ao Menu</a>
            </div>
            <div class="links">
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
            </div>
        </div>

        <div class="container">
            <h1><i class="fas fa-warehouse"></i> Produtos no Estoque</h1>
            <form class="search-bar" method="GET">
                <input type="text" name="search" placeholder="Pesquisar produtos..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit"><i class="fas fa-search"></i> Buscar</button>
            </form>
            <button class="btn-adicionar" onclick="window.location.href='novoproduto.php'">
                <i class="fas fa-plus"></i> Adicionar Novo Produto
            </button>
            
            <div class="card-container">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="card">';
                        echo '<img src="' . htmlspecialchars($row["imagem"]) . '" alt="' . htmlspecialchars($row["nome_produto"]) . '">';
                        echo '<h3>' . htmlspecialchars($row["nome_produto"]) . '</h3>';
                        echo '<p><strong>Categoria:</strong> ' . htmlspecialchars($row["categoria"]) . '</p>';
                        echo '<p><strong>Preço Compra:</strong> R$ ' . number_format(floatval($row["valor_compra"]), 2, ',', '.') . '</p>';
                        echo '<p><strong>Preço Venda:</strong> R$ ' . number_format(floatval($row["valor_venda"]), 2, ',', '.') . '</p>';
                        if ($row["promocao"] > 0 && $row["valor_promocional"] !== null) {
                            echo '<p class="promocao"><strong>Promoção:</strong> ' . $row["promocao"] . '% OFF</p>';
                            echo '<p><strong>Preço Promocional:</strong> R$ ' . number_format(floatval($row["valor_promocional"]), 2, ',', '.') . '</p>';
                        }
                        echo '<p><strong>Estoque:</strong> ' . $row["qtde"] . '</p>';
                        echo '<form action="editarproduto.php" method="GET" style="display:inline;">';
                        echo '<input type="hidden" name="produto_id" value="' . $row["id_produto"] . '">';
                        echo '<button type="submit" class="btn-editar"><i class="fas fa-edit"></i> Editar</button>';
                        echo '</form>';
                        echo '<form action="removerproduto.php" method="POST" id="form-remover' . $row["id_produto"] . '">';
                        echo '<input type="hidden" name="produto_id" value="' . $row["id_produto"] . '">';
                        echo '<button type="button" class="btn-remover" onclick="confirmarRemocao(' . $row["id_produto"] . ', \'' . htmlspecialchars($row["nome_produto"], ENT_QUOTES) . '\')"><i class="fas fa-trash"></i> Remover</button>';
                        echo '</form>';
                        echo '</div>';
                    }
                } else {
                    echo '<p style="text-align:center; width:100%;">Nenhum produto encontrado.</p>';
                }
                $mysqli->close();
                ?>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            const toggleBtn = document.querySelector('.toggle-btn');
            if (sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
                content.classList.remove('sidebar-open');
                toggleBtn.classList.remove('hidden');
            } else {
                sidebar.classList.add('open');
                content.classList.add('sidebar-open');
                toggleBtn.classList.add('hidden');
            }
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            const toggleBtn = document.querySelector('.toggle-btn');
            sidebar.classList.remove('open');
            content.classList.remove('sidebar-open');
            toggleBtn.classList.remove('hidden');
        }

        function confirmarRemocao(produtoId, produtoNome) {
            if (confirm(`Tem certeza que deseja remover "${produtoNome}"?`)) {
                document.getElementById(`form-remover${produtoId}`).submit();
            }
        }
    </script>

<footer>
        <div class="footer-content">
            <p>Todos os direitos reservados © <?php echo date('Y'); ?></p>
            <p>Contato: <a href="https://wa.me/+5547984617515" target="_blank"><i class="fab fa-whatsapp"></i> (47)984617515</a></p>
        </div>
    </footer>

</body>
</html>