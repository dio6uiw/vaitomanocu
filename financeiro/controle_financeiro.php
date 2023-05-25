<!DOCTYPE html>
<html>
<head>
    <title>Controle Financeiro</title>
</head>
<body>
    <h1>Controle Financeiro</h1>

    <h2>Cadastro de Devedor</h2>
    <form method="post">
        <label for="devedor">Nome do Devedor:</label>
        <input type="text" id="devedor" name="devedor">
        <br><br>
        <button type="button" onclick="adicionarParcela()">Adicionar Parcela</button>
    </form>

    <h2>Parcelas devidas</h2>
    <div id="parcelas"></div>
    <br>
    <button type="submit" name="submit">Salvar</button>

    <script>
        let numParcelas = 0;

        function adicionarParcela() {
            numParcelas++;
            const divParcela = document.createElement("div");

            const valorLabel = document.createElement("label");
            valorLabel.innerHTML = `Valor da Parcela ${numParcelas}:`;
            divParcela.appendChild(valorLabel);

            const valorInput = document.createElement("input");
            valorInput.type = "text";
            valorInput.name = `valor${numParcelas}`;
            divParcela.appendChild(valorInput);

            const dataLabel = document.createElement("label");
            dataLabel.innerHTML = `Data da Parcela ${numParcelas}:`;
            divParcela.appendChild(dataLabel);

            const dataInput = document.createElement("input");
            dataInput.type = "date";
            dataInput.name = `data${numParcelas}`;
            divParcela.appendChild(dataInput);

            document.getElementById("parcelas").appendChild(divParcela);
        }
    </script>

    <?php
    if (isset($_POST['submit'])) {
        // Estabeleça a conexão com o banco de dados

        $host = "localhost";
        $usuario = "root";
        $senha = "";
        $banco = "financeiro";

        $conn = new mysqli($host, $usuario, $senha, $banco);

        // Verifique se a conexão foi estabelecida corretamente

        if ($conn->connect_error) {
            die("Erro de conexão: " . $conn->connect_error);
        }

        // Receba os dados do formulário

        $devedor = $_POST['devedor'];
        $parcelas = [];

        foreach ($_POST as $key => $value) {
            if (strpos($key, 'valor') !== false) {
                $index = substr($key, 5);
                $parcelas[$index]['valor'] = $value;
            }

            if (strpos($key, 'data') !== false) {
                $index = substr($key, 4);
                $parcelas[$index]['data'] = $value;
            }
        }

        // Insira os dados no banco de dados

        $sql = "INSERT INTO controle_financeiro (devedor, valor, data) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        foreach ($parcelas as $parcela) {
            $valor = $parcela['valor'];
            $data = $parcela['data'];
            $stmt->bind_param("sss", $devedor, $valor, $data);
            $stmt->execute();
        }

        $stmt->close();
        $conn->close();

        echo "<script>alert('Dados salvos com sucesso!'); location.reload();</script>";
    }
    ?>
</body>
</html>
