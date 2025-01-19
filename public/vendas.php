<?php
require_once __DIR__ . '/../config/db.php';

$pdo = getConnection();

$stmt = $pdo->query("SELECT v.IdVenda, c.Nome AS Cliente, v.DataVenda, v.ValorTotal
                     FROM Venda v
                     JOIN Cliente c ON v.IdCliente = c.IdCliente");

echo "<h2>Pedidos</h2>";
echo "<table>";
echo "<thead>";
echo "<tr><th>ID</th><th>Cliente</th><th>Data</th><th>Valor Total</th></tr>";
echo "</thead>";
echo "<tbody>";

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>
            <td>{$row['IdVenda']}</td>
            <td>{$row['Cliente']}</td>
            <td>{$row['DataVenda']}</td>
            <td>R$ " . number_format($row['ValorTotal'], 2, ',', '.') . "</td>
          </tr>";

    $items = $pdo->prepare("SELECT p.Nome, vi.Quantidade, vi.ValorUnitario
                            FROM Venda_Item vi
                            JOIN Produto p ON vi.IdProduto = p.IdProduto
                            WHERE vi.IdVenda = ?");
    $items->execute([$row['IdVenda']]);
    echo "<tr><td colspan='4'><ul>";
    while ($item = $items->fetch(PDO::FETCH_ASSOC)) {
        echo "<li>{$item['Nome']} - Quantidade: {$item['Quantidade']} - Preço Unitário: R$ " 
             . number_format($item['ValorUnitario'], 2, ',', '.') . "</li>";
    }
    echo "</ul></td></tr>";
}

echo "</tbody>";
echo "</table>";
?>
