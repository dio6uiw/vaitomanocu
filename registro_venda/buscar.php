<?php
	//Conexão com o banco de dados
	$host = "localhost";
	$user = "root";
	$pass = "";
	$db = "clientes";

	$conexao = mysqli_connect($host, $user, $pass, $db);

	//Verifica se o parâmetro 'nome' foi enviado via POST
	if(isset($_POST['nome'])) {
		$nome = $_POST['nome'];
        $query = "SELECT * FROM tabela_clientes WHERE nome LIKE '%$nome%'";
		$resultado = mysqli_query($conexao, $query);

		$clientes = array();

		if(mysqli_num_rows($resultado) > 0) {
			while($row = mysqli_fetch_array($resultado)) {
				$clientes[] = array(
					'nome' => $row['nome'],
					'telefone' => $row['telefone'],
					'cpf' => $row['cpf'],
					'endereco' => $row['endereco']
				);
			}
		}

		echo json_encode($clientes);
	}

	//Fecha a conexão com o banco de dados
	mysqli_close($conexao);
?>
