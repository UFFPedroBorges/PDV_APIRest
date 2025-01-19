<?php
require_once __DIR__ . '/../config/db.php';

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

try {
    $pdo = getConnection();

	if ($method !== 'POST') {
		http_response_code(405);
		echo json_encode(["mensagem" => "Método não permitido"]);
		exit;
	}

	$data = json_decode(file_get_contents("php://input"), true);

	// Verifica se email e senha foram enviados
	if (!isset($data['email'], $data['senha'])) {
		http_response_code(400);
		echo json_encode(["mensagem" => "Email e senha são obrigatórios"]);
		exit;
	}

	$email = $data['email'];
	$senha = $data['senha'];

    $stmt = $pdo->prepare("SELECT IdUsuario, Nome, NivelAcesso FROM Usuario WHERE Email = :email AND Senha = :senha");
    $stmt->execute(['email' => $email, 'senha' => $senha]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        http_response_code(401);
        echo json_encode(["mensagem" => "Credenciais inválidas"]);
        exit;
    }

    // Gerar token simples (base64 para demonstração)
    $token = base64_encode(json_encode([
        'idUsuario' => $usuario['IdUsuario'],
        'nome' => $usuario['Nome'],
        'nivelAcesso' => $usuario['NivelAcesso'],
        'exp' => time() + 3600 // Expira em 1 hora
    ]));

    echo json_encode([
        "mensagem" => "Login realizado com sucesso",
        "token" => $token
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["mensagem" => "Erro no servidor", "erro" => $e->getMessage()]);
}
?>