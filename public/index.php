<?php require_once __DIR__ . '/../config/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loja de Tintas</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/script.js" defer></script>
</head>
<body>
    <header>
        <h1>Loja de Tintas</h1>
        <nav>
            <ul>
                <li><a href="clientes.php" class="menu-link" data-page="clientes.php">Clientes</a></li>
                <li><a href="produtos.php" class="menu-link" data-page="produtos.php">Produtos</a></li>
                <li><a href="vendas.php" class="menu-link" data-page="vendas.php">Pedidos</a></li>
                <li><a href="titulos.php" class="menu-link" data-page="titulos.php">Títulos</a></li>
            </ul>
        </nav>
    </header>
    <main id="content">
        <p>Bem-vindo ao sistema da Loja de Tintas. Use o menu para navegar.</p>
    </main>
</body>
</html>