<?php
include('conexao.php');
session_start();

if (isset($_POST['nome_cliente']) && isset($_POST['senha'])) {
    $nome_cliente = $_POST['nome_cliente'];
    $senha = $_POST['senha'];

    $stmt = $mysqli->prepare("SELECT * FROM cliente WHERE nome_cliente COLLATE utf8mb4_general_ci = ?");
    $stmt->bind_param("s", $nome_cliente);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $cliente = $result->fetch_assoc();

        if ($senha === $cliente['senha']) {
            $_SESSION['id_cliente'] = $cliente['id_cliente'];
            $_SESSION['nome_cliente'] = $cliente['nome_cliente'];
            header("Location: painelcliente.php");
            exit();
        } else {
            $erro = "Nome de usuário ou senha incorretos";
        }
    } else {
        $erro = "Nome de usuário ou senha incorretos";
    }
}
if(isset($_POST['nome_cliente']) && isset($_POST['senha'])){
    if(strlen(string: $_POST['nome_cliente']) == 0){
        echo "Usuário não informado";
    }else if(strlen(string: $_POST['senha']) == 0){
        echo "Preencha sua senha";
    }else{
        $usuario = $mysqli->real_escape_string(string: $_POST['nome_cliente']);
        $senha = $mysqli->real_escape_string(string: $_POST['senha']);
        $sql_code = "SELECT * FROM usuario WHERE UPPER(nome) = UPPER('$usuario') AND senha = '$senha'";
        $sql_query = $mysqli->query(query: $sql_code) or die('Falha na execução do código SQL: '.$mysqli->error);
        $quantidade = $sql_query->num_rows;

        if ($quantidade == 1){
            $usuario = $sql_query->fetch_assoc();
            if(!isset($_SESSION)){
                session_start();
            }
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nome'] = $usuario['nome'];
            header(header: "Location: ../paineldeacesso.php");
        }else{
            echo "Falha ao logar! Usuário ou senha incorretos";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Cliente - AlimentaSE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-image: url("imagens/fundo_site.png");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            overflow: hidden;
            padding: 20px;
        }
        .login-container {
            text-align: center;
            z-index: 2;
        }
        .logo {
            font-size: 4em;
            font-weight: bold;
            color: rgb(28, 151, 31);
            margin-bottom: 20px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.15);
            animation: fadeInLogo 1s ease-in-out;
        }
        @keyframes fadeInLogo {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .tela-login {
            background-color: #ff7f2a;
            border-radius: 20px;
            padding: 40px 60px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 400px;
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        h1 {
            color: rgb(255, 255, 255);
            font-size: 2em;
            margin-bottom: 15px;
            font-weight: 500;
        }
        .erro {
            color: #dc3545;
            font-size: 0.9em;
            margin-bottom: 15px;
            text-align: center;
        }
        input {
            width: 100%;
            padding: 12px 15px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1em;
            outline: none;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        input:focus {
            border-color: #ff7f2a;
            box-shadow: 0 0 6px rgba(255, 127, 42, 0.2);
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            border: none;
            border-radius: 6px;
            color: white;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.1s ease;
            margin: 10px 0;
        }
        button:hover {
            background-color: #218838;
            transform: scale(1.02);
        }
        button:active {
            transform: scale(0.98);
        }
        .link-senha {
            margin-top: 10px;
        }
        .link-senha a {
            color:rgb(47, 121, 53);
            text-decoration: none;
            font-size: 0.9em;
            transition: color 0.3s ease;
        }
        .link-senha a:hover {
            color:rgb(54, 206, 49);
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .tela-login {
                padding: 30px;
                max-width: 320px;
            }
            .logo {
                font-size: 3em;
            }
            h1 {
                font-size: 1.5em;
            }
            input, button {
                font-size: 0.9em;
                padding: 10px;
            }
        }
        @media (max-width: 480px) {
            .tela-login {
                padding: 20px;
                max-width: 280px;
            }
            .logo {
                font-size: 2.5em;
            }
        }
        
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">AlimentaSE</div>
        <div class="tela-login">
            <h1><i class="fas fa-user"></i> Login Cliente</h1>
            <?php if (isset($erro)) echo "<p class='erro'>$erro</p>"; ?>
            <form action="" method="POST">
                <input type="text" name="nome_cliente" placeholder="Nome de Usuário" required autocomplete="off">
                <input type="password" name="senha" placeholder="Senha" required>
                <button type="submit"><i class="fas fa-sign-in-alt"></i> Entrar</button>
                <div class="link-senha">
                    <a href="esqsenha.php">Esqueci minha senha</a>
                </div>
            </form>
        </div>
    </div>
    

</body>
</html>