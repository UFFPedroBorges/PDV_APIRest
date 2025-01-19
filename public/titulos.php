<?php
require_once __DIR__ . '/../config/db.php';

$pdo = getConnection();

$stmt = $pdo->query("SELECT * FROM Titulo");

echo "<h2>Títulos</h2>";
echo "<table>";
echo "<thead>";
echo "<tr><th>ID</th><th>ID Venda</th><th>Parcela</th><th>Valor</th><th>Pago</th><th>Data Emissão</th><th>Data Vencimento</th><th>Data Pago</th></tr>";
echo "</thead>";
echo "<tbody>";

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>
            <td>{$row['IdTitulo']}</td>
            <td>{$row['IdVenda']}</td>
            <td>{$row['Parcela']}</td>
            <td>R$ " . number_format($row['ValorTitulo'], 2, ',', '.') . "</td>
            <td>R$ " . number_format($row['ValorPago'] ?: 0, 2, ',', '.') . "</td>
            <td>{$row['DataEmissao']}</td>
            <td>{$row['DataVencimento']}</td>
            <td>" . ($row['DataPago'] ?: '-') . "</td>
          </tr>";
}

echo "</tbody>";
echo "</table>";
?>
