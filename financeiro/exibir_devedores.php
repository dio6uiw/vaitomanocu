<!DOCTYPE html>
<html>

<head>
  <title>Lista de Devedores</title>
  <style>
    table {
      border-collapse: collapse;
      width: 100%;
    }

    th,
    td {
      border: 1px solid black;
      padding: 8px;
      text-align: left;
    }

    .nao-pago {
      background-color: red;
      color: white;
    }

    .pago {
      background-color: green;
      color: white;
    }
  </style>
</head>

<body>
  <h1>Lista de Devedores</h1>

  <form method="GET" action="">
    <label for="dataInicial">Data Inicial:</label>
    <input type="date" id="dataInicial" name="dataInicial">
    <label for="dataFinal">Data Final:</label>
    <input type="date" id="dataFinal" name="dataFinal">
    <label for="filtroNome">Filtrar por Nome:</label>
    <input type="text" id="filtroNome" name="filtroNome">
    <input type="submit" value="Filtrar">
    <button onclick="gerarPDF()">Gerar PDF</button>
  </form>

  <table id="tabelaDevedores">
    <tr>
      <th>Nome do Devedor</th>
      <th>Valor da Parcela</th>
      <th>Data de Pagamento</th>
      <th>Pago</th>
    </tr>

    <?php
    // Configurações do banco de dados
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "cadastro_contasareceber";

    // Criar a conexão com o banco de dados
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar a conexão
    if ($conn->connect_error) {
      die("Falha na conexão com o banco de dados: " . $conn->connect_error);
    }

    // Filtrar por datas se fornecidas
    $whereClause = "";
    $dataInicial = $_GET['dataInicial'];
    $dataFinal = $_GET['dataFinal'];
    $filtroNome = $_GET['filtroNome'];

    if (!empty($dataInicial) && !empty($dataFinal)) {
      $whereClause .= " AND data_pagamento BETWEEN '$dataInicial' AND '$dataFinal'";
    }

    if (!empty($filtroNome)) {
      $whereClause .= " AND nome_devedor LIKE '%$filtroNome%'";
    }

    // Consulta SQL para obter os devedores com os filtros
    $sql = "SELECT id, nome_devedor, valor_parcela, data_pagamento, pago FROM contas_a_receber WHERE 1=1 $whereClause";
    $result = $conn->query($sql);

    if ($result) {
      $totalParcelas = 0; // Variável para armazenar a soma dos valores das parcelas

      // Exibir os devedores em formato de tabela
      while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['nome_devedor'] . "</td>";
        echo "<td>" . $row['valor_parcela'] . "</td>";
        echo "<td>" . $row['data_pagamento'] . "</td>";

        // Exibir a célula "Pago" com base no valor do campo "pago"
        $pagoClass = $row['pago'] ? 'pago' : 'nao-pago';
        $pagoText = $row['pago'] ? 'Pago' : 'Não Pago';
        echo "<td class='$pagoClass'>$pagoText</td>";

        echo "</tr>";

        $totalParcelas += $row['valor_parcela']; // Somar o valor da parcela ao total
      }

      // Exibir a soma das parcelas no final da tabela
      echo "<tr>";
      echo "<td><strong>Total:</strong></td>";
      echo "<td><strong>" . $totalParcelas . "</strong></td>";
      echo "<td></td>";
      echo "<td></td>";
      echo "</tr>";
    } else {
      echo "<tr><td colspan='4'>Nenhum devedor encontrado.</td></tr>";
    }

    // Fechar a conexão com o banco de dados
    $conn->close();
    ?>
  </table>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.10.2/jspdf.umd.min.js"></script>
  <script>
    function gerarPDF() {
      const doc = new jsPDF();
      const table = document.getElementById('tabelaDevedores');
      const rows = table.getElementsByTagName('tr');

      for (let i = 0; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        for (let j = 0; j < cells.length; j++) {
          const cell = cells[j];
          doc.cell(10, 10, 80, 10, cell.innerText, i);
        }
      }

      doc.save('lista_devedores.pdf');
    }

    function marcarPagamento(selectElement) {
      const row = selectElement.parentNode.parentNode;
      const pagoCell = row.querySelector('td:nth-child(4)');
      const value = selectElement.value;
      const id = row.getAttribute('data-id');

      // Remover a classe "nao-pago" e adicionar a classe "pago" quando marcado como pago
      if (value === 'sim') {
        pagoCell.classList.remove('nao-pago');
        pagoCell.classList.add('pago');
        pagoCell.innerText = 'Pago';
        atualizarStatusPagamento(id, true);
      } else if (value === 'nao') {
        pagoCell.classList.remove('pago');
        pagoCell.classList.add('nao-pago');
        pagoCell.innerText = 'Não Pago';
        atualizarStatusPagamento(id, false);
      }
    }

    function atualizarStatusPagamento(id, pago) {
      const xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
          // Callback da requisição AJAX
          console.log('Status de pagamento atualizado com sucesso!');
        }
      };
      xhttp.open('POST', 'atualizar_status_pagamento.php', true);
      xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
      xhttp.send(`id=${id}&pago=${pago}`);
    }
  </script>
</body>

</html>
