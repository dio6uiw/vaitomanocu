<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "clientes";

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica se a conexão foi bem-sucedida
if ($conn->connect_error) {
  die("Conexão falhou: " . $conn->connect_error);
}

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nome = $_POST["nome"];
  $telefone = $_POST["telefone"];
  $cpf = $_POST["cpf"];
  $endereco = $_POST["endereco"];

  // Insere os dados no banco de dados
  $sql = "INSERT INTO tabela_clientes (nome, telefone, cpf, endereco)
  VALUES ('$nome', '$telefone', '$cpf', '$endereco')";

  if ($conn->query($sql) === TRUE) {
    echo "<a href='index.html'></a>";
  } else {
    echo "<a href='index.html'></a>";
  }
}   

  header("Location: index.html");

// Fecha a conexão
$conn->close();
?>
