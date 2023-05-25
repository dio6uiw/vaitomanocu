<!DOCTYPE html>
<html>
<head>
    <title>Sistema de Controle de Pagamento</title>
    <script>
        function calcularParcelas() {
            var valorTotal = parseFloat(document.getElementById('valor').value);
            var parcelas = parseInt(document.getElementById('parcelas').value);

            var valorParcela = valorTotal / parcelas;

            var parcelasContainer = document.getElementById('parcelasContainer');
            parcelasContainer.innerHTML = '';

            for (var i = 1; i <= parcelas; i++) {
                var input = document.createElement('input');
                input.type = 'date';
                input.name = 'dataParcela[]';
                input.required = true;

                var label = document.createElement('label');
                label.innerHTML = 'Data da Parcela ' + i + ': ';
                label.appendChild(input);

                parcelasContainer.appendChild(label);

                var hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'valorParcela[]';
                hiddenInput.value = valorParcela.toFixed(2);

                parcelasContainer.appendChild(hiddenInput);
            }
        }
    </script>
</head>
<body>
    <h1>Sistema de Controle de Pagamento</h1>
    <form id="paymentForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <label for="devedor">Nome do Devedor:</label>
        <input type="text" id="devedor" name="devedor" required><br><br>

        <label for="valor">Valor Total da Compra:</label>
        <input type="number" id="valor" name="valor" step="0.01" required><br><br>

        <label for="parcelas">Quantidade de Parcelas:</label>
        <input type="number" id="parcelas" name="parcelas" min="1" required><br><br>

        <button type="button" onclick="calcularParcelas()">Calcular Parcelas</button><br><br>

        <div id="parcelasContainer"></div><br>

        <input type="submit" value="Enviar">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "controle_financeiro";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Erro na conexão com o banco de dados: " . $conn->connect_error);
        }

        $devedor = $_POST['devedor'];
        $valorTotal = $_POST['valor'];
        $parcelas = $_POST['parcelas'];
        $dataParcela = $_POST['dataParcela'];
        $valorParcela = $_POST['valorParcela'];

        $sql = "INSERT INTO pagamentos (devedor, valor_total, parcelas) VALUES ('$devedor', '$valorTotal', '$parcelas')";

        if ($conn->query($sql) === TRUE) {
            $pagamentoId = $conn->insert_id;

            $numParcelas = count($dataParcela);

            for ($i = 0; $i < $numParcelas; $i++) {
                $data = $dataParcela[$i];
                $valor = $valorParcela[$i];

                $sqlParcela = "INSERT INTO parcelas (pagamento_id, data, valor) VALUES ('$pagamentoId', '$data', '$valor')";
                $conn->query($sqlParcela);
            }

            echo "Pagamento registrado com sucesso!";
        } else {
            echo "Erro ao registrar pagamento: " . $conn->error;
        }

        $conn->close();
    }
    ?>

    <h2>Tabela de Devedores</h2>

    <form id="filterForm" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="filterDevedor">Filtrar por Nome do Devedor:</label>
        <input type="text" id="filterDevedor" name="filterDevedor">
        <br><br>
        <label for="filterPeriodoInicio">Filtrar por Período de:</label>
        <input type="date" id="filterPeriodoInicio" name="filterPeriodoInicio">
        <label for="filterPeriodoFim">até:</label>
        <input type="date" id="filterPeriodoFim" name="filterPeriodoFim">
        <br><br>
        <input type="submit" value="Filtrar">
    </form>

    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "controle_financeiro";
    
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Erro na conexão com o banco de dados: " . $conn->connect_error);
    }

    $sql = "SELECT pagamentos.devedor, parcelas.data, parcelas.valor FROM pagamentos JOIN parcelas ON pagamentos.id = parcelas.pagamento_id";

    // Verificar os parâmetros de filtro
    $filterDevedor = $_GET['filterDevedor'];
    $filterPeriodoInicio = $_GET['filterPeriodoInicio'];
    $filterPeriodoFim = $_GET['filterPeriodoFim'];

    if (!empty($filterDevedor) || (!empty($filterPeriodoInicio) && !empty($filterPeriodoFim))) {
        $sql .= " WHERE ";

        if (!empty($filterDevedor)) {
            $sql .= "pagamentos.devedor LIKE '%$filterDevedor%'";

            if (!empty($filterPeriodoInicio) && !empty($filterPeriodoFim)) {
                $sql .= " AND ";
            }
        }

        if (!empty($filterPeriodoInicio) && !empty($filterPeriodoFim)) {
            $sql .= "parcelas.data BETWEEN '$filterPeriodoInicio' AND '$filterPeriodoFim'";
        }
    }

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table id='tabelaDevedores'>
                <tr>
                    <th>Devedor</th>
                    <th>Data da Parcela</th>
                    <th>Valor da Parcela</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row["devedor"]."</td>
                    <td>".$row["data"]."</td>
                    <td>".$row["valor"]."</td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "Nenhum dado encontrado na tabela.";
    }

    $conn->close();
    ?>
</body>
</html>
