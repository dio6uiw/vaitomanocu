<?php
// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "clientes";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Obtenção do parâmetro de pesquisa
$nome = $_POST['nome'];

// Consulta ao banco de dados
$sql = "SELECT nome FROM tabela_clientes WHERE nome LIKE '%$nome%'";
$result = $conn->query($sql);

$sugestoes = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sugestoes[] = $row;
    }
}

// Fechamento da conexão com o banco de dados
$conn->close();

// Retorno dos resultados no formato JSON
echo json_encode($sugestoes);
?>
