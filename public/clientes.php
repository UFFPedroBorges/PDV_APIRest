<?php
require_once __DIR__ . '/../config/db.php';

$pdo = getConnection();

$stmt = $pdo->query("SELECT * FROM Cliente");

echo "<h2>Clientes</h2>";
echo "<table>";
echo "<thead>";
echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Telefone</th><th>Endereço</th></tr>";
echo "</thead>";
echo "<tbody>";

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>
            <td>{$row['IdCliente']}</td>
            <td>{$row['Nome']}</td>
            <td>{$row['Email']}</td>
            <td>{$row['Telefone']}</td>
            <td>{$row['Endereco']}</td>
          </tr>";
}

echo "</tbody>";
echo "</table>";
?>
