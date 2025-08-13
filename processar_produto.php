<?php
include('conexao.php'); // Arquivo para conexão com o banco de dados

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_produto = $_POST['nome'];  // Nome do produto
    $valor_compra = $_POST['valor_compra'];
    $valor_venda = $_POST['valor_venda'];
    $qtde = $_POST['quantidade'];  // Quantidade do produto
    $imagem = $_FILES['imagem'];   // Arquivo da imagem

    // Cria a pasta uploads caso não exista
    $pasta_uploads = 'uploads/';
    if (!is_dir($pasta_uploads)) {
        mkdir($pasta_uploads, 0777, true);
    }

    // Verifica se a imagem foi enviada corretamente
    if ($imagem['error'] === UPLOAD_ERR_OK) {
        $extensao = pathinfo($imagem['name'], PATHINFO_EXTENSION);
        $nome_arquivo = uniqid() . '.' . $extensao;  // Gera um nome único para o arquivo
        $caminho_destino = $pasta_uploads . $nome_arquivo;

        // Move o arquivo para a pasta de uploads
        if (move_uploaded_file($imagem['tmp_name'], $caminho_destino)) {
            // Insere os dados no banco de dados
            $sql = "INSERT INTO Produto (nome_produto, valor_compra, valor_venda, qtde, imagem) VALUES (?, ?, ?, ?, ?)";
            $stmt = $mysqli->prepare($sql);  // Prepara a consulta
            $stmt->bind_param("sddis", $nome_produto, $valor_compra, $valor_venda, $qtde, $caminho_destino); // Vincula os parâmetros

            // Executa a consulta e retorna a resposta
            if ($stmt->execute()) {
                echo "<script>alert('Produto adicionado com sucesso!'); window.location.href='painel.php';</script>";
            } else {
                echo "<script>alert('Erro ao adicionar o produto!'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Erro ao salvar a imagem!'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Erro no envio da imagem!'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Requisição inválida!'); window.history.back();</script>";
}
?>
