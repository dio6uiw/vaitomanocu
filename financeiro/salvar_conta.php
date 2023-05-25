<?php
// Configurações do banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cadastro_contasareceber";

// Receber os dados da requisição AJAX
$nomeDevedor = $_POST['nomeDevedor'];
$valorParcela = $_POST['valorParcela'];
$datasPagamento = $_POST['datasPagamento'];

// Criar a conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Preparar e executar a consulta SQL para inserir os dados
$sql = "INSERT INTO contas_a_receber (nome_devedor, valor_parcela, data_pagamento) VALUES";

foreach ($datasPagamento as $data) {
    $sql .= " ('$nomeDevedor', '$valorParcela', '$data'),";
}

$sql = rtrim($sql, ","); // Remover a última vírgula

if ($conn->query($sql) === TRUE) {
    echo "Conta a receber cadastrada com sucesso!";
} else {
    echo "Erro ao cadastrar a conta a receber: " . $conn->error;
}

// Fechar a conexão com o banco de dados
$conn->close();
?>
