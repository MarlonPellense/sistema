<?php
// Configurações de conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cantina";

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verifica se o ID do produto foi enviado
if (isset($_POST['produto_id'])) {
    $produto_id = $_POST['produto_id'];

    // Prepara a consulta para excluir o produto
     $sql = "UPDATE Produto SET stat = 'Oculto' WHERE id_produto = ?";
    
    // Prepara a declaração
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $produto_id); // "i" significa inteiro
        $stmt->execute();
        
        // Verifica se o produto foi excluído com sucesso
        if ($stmt->affected_rows > 0) {
            echo "Produto removido com sucesso!";
        } else {
            echo "Erro ao remover produto!";
        }

        $stmt->close();
    }
} else {
    echo "ID do produto não encontrado.";
}

$conn->close();

// Redireciona de volta para a página de estoque após a remoção
header("Location: painel.php");
exit;
?>
