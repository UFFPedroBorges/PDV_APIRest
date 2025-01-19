<?php
require_once __DIR__ . '/../config/db.php';

$pdo = getConnection();

$stmt = $pdo->query("SELECT * FROM Produto");

echo "<h2>Produtos</h2>";
echo "<table>";
echo "<thead>";
echo "<tr><th>ID</th><th>Nome</th><th>Preço</th><th>Estoque</th></tr>";
echo "</thead>";
echo "<tbody>";

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>
            <td>{$row['IdProduto']}</td>
            <td>{$row['Nome']}</td>
            <td>R$ " . number_format($row['Preco'], 2, ',', '.') . "</td>
            <td>{$row['Estoque']}</td>
          </tr>";
}

echo "</tbody>";
echo "</table>";
?>
