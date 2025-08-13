<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['id_cliente'])) {
    header("Location: index.php");
    exit();
}
$id_cliente = $_SESSION['id_cliente'];

$stmt_nome = $mysqli->prepare("SELECT nome_cliente FROM cliente WHERE id_cliente = ?");
$stmt_nome->bind_param("i", $id_cliente);
$stmt_nome->execute();
$result_nome = $stmt_nome->get_result();
$nome_cliente = ($result_nome && $result_nome->num_rows > 0) 
    ? $result_nome->fetch_assoc()['nome_cliente'] 
    : "Cliente não encontrado";
$stmt_nome->close();

$stmt_insti = $mysqli->prepare("SELECT instituicao FROM cliente WHERE id_cliente = ?");
$stmt_insti->bind_param("i", $id_cliente);
$stmt_insti->execute();
$result_insti = $stmt_insti->get_result();    
$insti_cliente = ($result_insti && $result_insti->num_rows > 0) 
    ? $result_insti->fetch_assoc()['instituicao'] 
    : "Instituição não encontrada";
$stmt_insti->close();


if (!isset($_SESSION['carrinho'][$id_cliente])) {
    $_SESSION['carrinho'][$id_cliente] = [];
}

// Consulta produtos promocionais com valor_venda
$promocoes = [];
$sql_promocoes = "SELECT id_produto, nome_produto, imagem, promocao, valor_venda FROM produto WHERE promocao > 0 AND stat = 'Ativo'";
$result_promocoes = $mysqli->query($sql_promocoes);
while ($row = $result_promocoes->fetch_assoc()) {
    $promocoes[] = $row;
}

// Consulta produtos normais com busca e filtro por categoria
$produtos = [];
$busca = isset($_GET['busca']) ? $_GET['busca'] : '';
$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';

$sql = "SELECT id_produto, nome_produto, valor_venda, promocao, imagem FROM produto WHERE nome_produto LIKE ? AND stat = 'Ativo'";
$params = ["%$busca%"];
$types = "s";

if (!empty($categoria)) {
    $sql .= " AND categoria = ?";
    $params[] = $categoria;
    $types .= "s";
}

$stmt = $mysqli->prepare($sql);

if ($stmt) {
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $produtos[] = $row;
    }

    $stmt->close();
} else {
    echo "Erro na consulta: " . $mysqli->error;
}

$tipos_carrinho = count($_SESSION['carrinho'][$id_cliente]);

// Buscar pedido pendente
$stmt_pedido = $mysqli->prepare("SELECT id_pedido, unidade, total FROM pedidos WHERE id_cliente = ? AND status = 'novo' ORDER BY data_pedido DESC LIMIT 1");
$stmt_pedido->bind_param("i", $id_cliente);
$stmt_pedido->execute();
$result_pedido = $stmt_pedido->get_result();
$mostrar_card = $result_pedido->num_rows > 0;
$pedido = $mostrar_card ? $result_pedido->fetch_assoc() : null;
$stmt_pedido->close();

$itens_pedido = [];
if ($mostrar_card) {
    $id_pedido = $pedido['id_pedido'];
    $stmt_itens = $mysqli->prepare("SELECT p.nome_produto, v.quantidade_vendida 
                                   FROM vendas v 
                                   JOIN produto p ON v.produto_id_produto = p.id_produto 
                                   WHERE v.id_pedido = ?");
    $stmt_itens->bind_param("i", $id_pedido);
    $stmt_itens->execute();
    $result_itens = $stmt_itens->get_result();
    while ($item = $result_itens->fetch_assoc()) {
        $itens_pedido[] = $item;
    }
    $stmt_itens->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel do Cliente - AlimentaSE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background-color: #f7fff3;
    margin: 0;
    color: #333;
    display: flex;
    flex-direction: column; 
    min-height: 100vh; 
    overflow-x: hidden;
    background-image: url("imagens/fundo_site.png");
    background-size: cover;
    background-position: center;
    background-repeat: repeat;
}

.navbar {
    background-color:rgb(255, 127, 42);
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
    box-sizing: border-box;
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

#profile-card {
    position: fixed;
    top: 50px;
    right: 0px;
    background-color:rgb(255, 255, 255);
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 10px;
}
        
#profile-card button {
    display: block;
    width: 100%;
    padding: 8px;
    margin: 5px 0;
    background-color:rgb(77, 185, 62);
    border: 1px solid #ddd;
    border-radius: 3px;
    cursor: pointer;
    color: white;
    font-weight: 1000;
}
        
#profile-card button:hover {
    background-color:rgb(50, 145, 41);
}

.hidden {
    display: none;
}

main {
    margin-top: 70px; 
    padding: 20px; 
}
.badge {
    position: absolute;
    top: -8px;
    right: -10px;
    background-color: red;
    color: white;
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 12px;
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
.promo-slider {
    width: 102%;
    margin: 20px auto;
    position: relative;
    overflow: hidden;
    height: 200px;
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.promo-slider .slides {
    display: flex;
    transition: transform 0.5s ease;
    height: 100%;
}
.promo-slider .slide {
    min-width: 100%;
    height: 100%;
    position: relative;
}
.promo-slider .slide img {
    width: 100%;
    max-height: 200px;
    object-fit: contain;
    object-position: center;
    position: absolute;
    top: 0;
    left: 0;
}
.promo-slider .overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 100%;
    background: linear-gradient(to bottom, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.8) 100%);
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    align-items: center;
    padding-bottom: 10px;
}
.promo-slider .slide h3 {
    color: #fff;
    font-size: 1.5em;
    margin: 0;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
}
.promo-slider .slide h1 {
    color: rgb(0, 255, 0);
    font-weight: bold;
    font-size: 2.5em;
    margin: 5px 0 10px;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
}
.promo-slider .slide .btn-add-promo {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background-color: rgba(255, 127, 42, 0.8);
    color: white;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.1s;
}
.promo-slider .slide .btn-add-promo:hover {
    background-color: rgba(230, 111, 36, 0.9);
    transform: scale(1.1);
}
.promo-slider .slide .btn-add-promo:active {
    transform: scale(0.9);
}
.promo-slider .slide .btn-add-promo i {
    font-size: 1.2em;
}
.promo-slider .prev, .promo-slider .next {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background-color: rgba(0,0,0,0.5);
    color: white;
    border: none;
    padding: 10px;
    cursor: pointer;
    z-index: 0;
}
.promo-slider .prev {
    left: 10px;
}
.promo-slider .next {
    right: 10px;
}
.promo-slider .dots {
    position: absolute;
    bottom: 10px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 5px;
}
.promo-slider .dot {
    width: 10px;
    height: 10px;
    background-color: #bbb;
    border-radius: 50%;
    cursor: pointer;
}
.promo-slider .dot.active {
    background-color: #ff7f2a;
}
.buscar {
    text-align: center;
    margin: 20px;
}
.buscar input {
    padding: 10px;
    width: 300px;
    border-radius: 5px;
    border: 1px solid #ccc;
}
.produtos {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    padding: 20px;
}
.produto {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin: 10px;
    padding: 15px;
    width: 220px;
    text-align: center;
    display: flex;
    flex-direction: column;
}
.produto img {
    width: 100%;
    height: 140px;
    object-fit: cover;
    border-radius: 8px;
}
.produto h3 {
    font-size: 18px;
    margin: 10px 0 5px;
}
.produto p {
    color: green;
    font-weight: bold;
    margin: 5px 0;
}
.controls {
    display: none;
    justify-content: center;
    align-items: center;
    gap: 10px;
    margin-top: 10px;
}
.controls button {
    background-color: #ff7f2a;
    border: none;
    color: white;
    padding: 5px 10px;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
}
.quantidade {
    font-weight: bold;
}
.btn-adicionar {
    background-color: #ff7f2a;
    color: white;
    border: none;
    padding: 8px 10px;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 10px;
}
#mensagem-adicionado {
    background-color: #d4edda;
    color: #155724;
    padding: 10px;
    border-radius: 5px;
    margin: 15px;
    text-align: center;
}
#card-pedido {
    position: fixed;
    top: 20px;
    right: 20px;
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    width: 280px;
    z-index: 1000;
}
#card-pedido h3 {
    margin-top: 0;
    color: #ff7f2a;
}
#card-pedido ul {
    list-style: none;
    padding: 0;
    margin: 10px 0;
}
#card-pedido ul li {
    font-size: 0.9em;
    margin-bottom: 5px;
}
#card-pedido h1 {
    margin: 5px 0;
    font-size: 0.9em;
}
#card-pedido button {
    width: 100%;
    padding: 8px;
    background-color: #ff7f2a;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    margin-top: 10px;
    transition: background-color 0.3s;
}
#card-pedido button:hover {
    background-color: #e66c1f;
}
@media (max-width: 600px) {
    .buscar input {
        width: 100%;
    }
    .produtos {
        padding: 10px;
    }
    .produto {
        width: 100%;
        max-width: 300px;
    }
    #card-pedido {
        width: 200px;
        top: 10px;
        right: 10px;
    }
    .promo-slider {
        height: 180px;
    }
    .promo-slider .slide h3 {
        font-size: 1.2em;
    }
    .promo-slider .slide h1 {
        font-size: 1em;
    }
    .promo-slider .slide img {
        max-height: 80px;
    }
    .promo-slider .slide .btn-add-promo {
        width: 35px;
        height: 35px;
    }
    .promo-slider .slide .btn-add-promo i {
        font-size: 1em;
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

.profile {
    cursor: pointer;
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
    <button class="toggle-btn" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>
    <div class="sidebar" id="sidebar">
        <button class="close-btn" onclick="closeSidebar()">
            <i class="fas fa-times"></i>
        </button>
        <h2><br>Categorias</h2>
        <a href="?categoria=" class="<?php echo $categoria == '' ? 'active' : ''; ?>">Todos</a>
        <a href="?categoria=Doces" class="<?php echo $categoria == 'Doces' ? 'active' : ''; ?>">Doces</a>
        <a href="?categoria=Salgados" class="<?php echo $categoria == 'Salgados' ? 'active' : ''; ?>">Salgados</a>
        <a href="?categoria=Bebidas" class="<?php echo $categoria == 'Bebidas' ? 'active' : ''; ?>">Bebidas</a>
        <a href="?categoria=Combos" class="<?php echo $categoria == 'Combos' ? 'active' : ''; ?>">Combos</a>
        <a href="?categoria=Promoção da Semana" class="<?php echo $categoria == 'Promoção da Semana' ? 'active' : ''; ?>">Promoção da Semana</a>
    </div>

    <div class="content" id="content">
        <div class="navbar">
            <div><a onclick="closeSidebar()" style="cursor: pointer">- - 🍉 AlimentaSE</a></div>
            <div>
                <a href="carrinho.php" id="link-carrinho">
                    🛒 Ver Carrinho
                    <?php if ($tipos_carrinho > 0): ?>
                        <span class="badge" id="badge"><?= $tipos_carrinho ?></span>
                    <?php else: ?>
                        <span class="badge" id="badge" style="display:none;"></span>
                    <?php endif; ?>
                </a>
                <a id="profile" class="profile" onclick="toggleClick()">Perfil</a>
                
            </div>
        </div>

        <!-- Container Animado de Slides para Produtos Promocionais -->
        <div class="promo-slider">
            <div class="slides">
                <?php foreach ($promocoes as $index => $promo): ?>
                    <div class="slide" data-index="<?= $index ?>" data-id="<?= $promo['id_produto'] ?>">
                        <img src="../<?= htmlspecialchars($promo['imagem']) ?>" alt="<?= htmlspecialchars($promo['nome_produto']) ?>">
                        <div class="overlay">
                            <h3><?= htmlspecialchars($promo['nome_produto']) ?></h3>
                            <h1><?= $promo['promocao'] ?>% OFF</h1>
                            <button class="btn-add-promo" onclick="addPromoToCart(<?= $promo['id_produto'] ?>)">
                                <i class="fas fa-cart-plus"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="prev" onclick="prevSlide()">❮</button>
            <button class="next" onclick="nextSlide()">❯</button>
            <div class="dots">
                <?php for ($i = 0; $i < count($promocoes); $i++): ?>
                    <div class="dot" data-index="<?= $i ?>" onclick="goToSlide(<?= $i ?>)"></div>
                <?php endfor; ?>
            </div>
        </div>

        <?php if ($mostrar_card && !empty($itens_pedido)): ?>
            <div id="card-pedido">
                <h3>Seu Pedido</h3>
                <ul>
                    <?php foreach ($itens_pedido as $item): ?>
                        <li><?= htmlspecialchars($item['nome_produto']) ?> (<?= $item['quantidade_vendida'] ?>)</li>
                    <?php endforeach; ?>
                </ul>
                <h1><strong>Código:</strong> <?= htmlspecialchars($pedido['id_pedido']) ?></h1>
                <h1><strong>Total:</strong> R$ <?= number_format($pedido['total'], 2, ',', '.') ?></h1>
                <button onclick="window.location.href='retirar_agora.php?pedido=<?= $pedido['id_pedido'] ?>'">Retirar Agora</button>
        <?php endif; ?>
        </div>

        <div id="profile-card" class="hidden">
            <a style="font-size: large; font-weight: bold; margin-right: 35px;"><?php echo $nome_cliente?><br></a>
            <a><?php echo $insti_cliente?><br></a>
            <button>Configurações</button>
            <button>Ajuda e Suporte</button>
            <button onclick="window.location.href='index.php'">Sair</button>
        </div>

        <div class="buscar">
            <form method="GET">
                <input type="text" name="busca" placeholder="Buscar por produto..." value="<?= htmlspecialchars($busca) ?>">
                <?php if (!empty($categoria)): ?>
                    <input type="hidden" name="categoria" value="<?= htmlspecialchars($categoria) ?>">
                <?php endif; ?>
            </form>
        </div>

        <div class="produtos">
            <?php foreach ($produtos as $produto): ?>
                <div class="produto" data-id="<?= $produto['id_produto'] ?>">
                    <img src="../<?= htmlspecialchars($produto['imagem']) ?>" alt="<?= htmlspecialchars($produto['nome_produto']) ?>">
                    <h3><?= htmlspecialchars($produto['nome_produto']) ?></h3>
                    <?php if ($produto['promocao'] > 0): ?>
                        <p>R$ <?= number_format($produto['valor_venda'], 2, ',', '.') ?> - <?= $produto['promocao'] ?>% OFF</p>
                        <button class="btn-adicionar" onclick="mostrarControles(this), addPromoToCart(<?= $produto['id_produto'] ?>)">Adicionar</button>
                    <?php endif; ?>

                    <?php if ($produto['promocao'] == 0): ?>
                        <p>R$ <?= number_format($produto['valor_venda'], 2, ',', '.') ?></p>
                        <button class="btn-adicionar" onclick="mostrarControles(this)">Adicionar</button>
                    <?php endif; ?>

                    <div class="controls">
                        <button onclick="alterarQuantidade(this, -1)">-</button>
                        <span class="quantidade">1</span>
                        <button onclick="alterarQuantidade(this, 1)">+</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="mensagem-adicionado" style="display:none;">Produto adicionado ao carrinho!</div>

    <script>
        // Funções para Sidebar
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

        // Funções para Carrossel de Promoções
        let currentSlide = 0;
        const slides = document.querySelectorAll('.promo-slider .slide');
        const dots = document.querySelectorAll('.promo-slider .dot');

        function showSlide(index) {
            if (index >= slides.length) index = 0;
            if (index < 0) index = slides.length - 1;
            currentSlide = index;
            document.querySelector('.promo-slider .slides').style.transform = `translateX(-${index * 100}%)`;
            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === index);
            });
        }

        function nextSlide() {
            showSlide(currentSlide + 1);
        }

        function prevSlide() {
            showSlide(currentSlide - 1);
        }

        function goToSlide(index) {
            showSlide(index);
        }

        // Auto-play
        setInterval(nextSlide, 4000);

        // Inicializa o primeiro slide
        showSlide(0);

        // Função para adicionar promoção ao carrinho
        function addPromoToCart(id_produto) {
            atualizarCarrinho(id_produto, 1, true);
        }

        function toggleClick(){
            const profileCard = document.getElementById("profile-card");

            profileCard.classList.toggle('hidden');
        }

        // Funções para Carrinho
        function mostrarControles(botao) {
            const container = botao.closest('.produto');
            botao.style.display = 'none';
            container.querySelector('.controls').style.display = 'flex';
            atualizarCarrinho(container.dataset.id, 1, false);
        }

        function alterarQuantidade(btn, valor) {
            const container = btn.closest('.produto');
            const span = container.querySelector('.quantidade');
            let quantidade = parseInt(span.textContent) + valor;

            if (quantidade < 1) quantidade = 1;
            if (quantidade > 10) quantidade = 10;

            span.textContent = quantidade;
            atualizarCarrinho(container.dataset.id, quantidade, false);
        }

        function atualizarCarrinho(id_produto, quantidade, is_promo = false) {
            const data = `id_produto=${id_produto}&quantidade=${quantidade}&ajax=1&is_promo=${is_promo}`;
            fetch('add_carrinho.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: data
            })
            .then(res => res.json())
            .then(data => {
                if (data.erro) {
                    console.error(data.erro);
                    return;
                }
                document.getElementById("mensagem-adicionado").style.display = "block";
                document.getElementById("badge").style.display = "inline-block";
                document.getElementById("badge").textContent = data.total_tipos;

                setTimeout(() => {
                    document.getElementById("mensagem-adicionado").style.display = "none";
                }, 3000);
            })
            .catch(err => console.error('Erro ao adicionar ao carrinho:', err));
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