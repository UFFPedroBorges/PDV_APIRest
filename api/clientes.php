<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/functions.php';

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

try {
    $pdo = getConnection();

	if ($method === 'GET') {
		// Endpoint: GET /clientes
		validarToken(); // Qualquer usuário logado pode acessar
		
		$stmt = $pdo->query("SELECT * FROM Cliente");
		$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($clientes);

	} elseif ($method === 'POST') {
		// Endpoint: POST /clientes
		validarToken(); // Qualquer usuário logado pode acessar
		
		$data = json_decode(file_get_contents("php://input"), true);

		if (!isset($data['nome'], $data['email'], $data['telefone'], $data['endereco'])) {
			http_response_code(400);
			echo json_encode(["mensagem" => "Dados incompletos para cadastro"]);
			exit;
		}

		$stmt = $pdo->prepare("INSERT INTO Cliente (Nome, Email, Telefone, Endereco) VALUES (:nome, :email, :telefone, :endereco)");
		$stmt->execute([
			':nome' => $data['nome'],
			':email' => $data['email'],
			':telefone' => $data['telefone'],
			':endereco' => $data['endereco'],
		]);

		$idCliente = $pdo->lastInsertId();
		echo json_encode([
			"mensagem" => "Cliente cadastrado com sucesso",
			"idCliente" => $idCliente
		]);
	} else {
		http_response_code(405);
		echo json_encode(["mensagem" => "Método não permitido"]);
	}
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["mensagem" => "Erro no servidor", "erro" => $e->getMessage()]);
}
?>