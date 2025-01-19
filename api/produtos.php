<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/functions.php';

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

try {
    $pdo = getConnection();

    if ($method === 'GET') {
        // Endpoint: GET /produtos
        validarToken(); // Qualquer usuário logado pode acessar

        $stmt = $pdo->query("SELECT * FROM Produto");
        $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($produtos);

    } elseif ($method === 'POST') {
        // Endpoint: POST /produtos
        validarToken(1); // Apenas usuários com nível de acesso 1 (Administrador)

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['nome'], $data['preco'], $data['estoque'])) {
            http_response_code(400);
            echo json_encode(["mensagem" => "Dados incompletos para cadastro"]);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO Produto (Nome, Preco, Estoque) VALUES (:nome, :preco, :estoque)");
        $stmt->execute([
            ':nome' => $data['nome'],
            ':preco' => $data['preco'],
            ':estoque' => $data['estoque']
        ]);

        $idProduto = $pdo->lastInsertId();
        echo json_encode([
            "mensagem" => "Produto cadastrado com sucesso",
            "idProduto" => $idProduto
        ]);

    } else {
        // Método não permitido
        http_response_code(405);
        echo json_encode(["mensagem" => "Método não permitido"]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["mensagem" => "Erro no servidor", "erro" => $e->getMessage()]);
}
?>
