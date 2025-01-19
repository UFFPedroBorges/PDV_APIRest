<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/functions.php';

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

try {
    $pdo = getConnection();

    if ($method === 'GET') {
        // Endpoint: GET /titulos?idCliente=1 ou /titulos?idVenda=5
        validarToken(); // Qualquer usuário logado pode acessar

        $idCliente = isset($_GET['idCliente']) ? $_GET['idCliente'] : null;
        $idVenda = isset($_GET['idVenda']) ? $_GET['idVenda'] : null;

        if ($idCliente) {
            $stmt = $pdo->prepare(
                "SELECT T.*, C.IdCliente, C.Nome AS NomeCliente
                 FROM Titulo T
				 JOIN Venda V ON T.IdVenda = V.IdVenda
                 JOIN Cliente C ON V.IdCliente = C.IdCliente
                 WHERE C.IdCliente = :idCliente"
            );
            $stmt->execute([':idCliente' => $idCliente]);
            $titulos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } elseif ($idVenda) {
            $stmt = $pdo->prepare(
                "SELECT T.*, C.IdCliente, C.Nome AS NomeCliente
                 FROM Titulo T
				 OIN Venda V ON T.IdVenda = V.IdVenda
                 JOIN Cliente C ON V.IdCliente = C.IdCliente
                 WHERE T.IdVenda = :idVenda"
            );
            $stmt->execute([':idVenda' => $idVenda]);
            $titulos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            http_response_code(400);
            echo json_encode(["mensagem" => "Informe pelo menos um parâmetro: idCliente ou idVenda"]);
            exit;
        }

        echo json_encode($titulos);

    } elseif ($method === 'PUT') {
        // Endpoint: PUT /titulos/{idTitulo}
        validarToken(1); // Apenas usuários com nível de acesso 1 (Administrador)
		
		$idTitulo = isset($_GET['idTitulo']) ? $_GET['idTitulo'] : null;

        if (!$idTitulo || !is_numeric($idTitulo)) {
            http_response_code(400);
            echo json_encode(["mensagem" => "ID do título inválido"]);
            exit;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        // Verifica se pelo menos um campo foi enviado para atualização
        $camposAtualizaveis = [
            'valorTitulo' => isset($data['valorTitulo']) ? $data['valorTitulo'] : null,
            'valorPago' => isset($data['valorPago']) ? $data['valorPago'] : null,
            'dataPago' => isset($data['dataPago']) ? $data['dataPago'] : null,
            'dataVencimento' => isset($data['dataVencimento']) ? $data['dataVencimento'] : null
        ];

        // Filtra os campos enviados
        $camposEnviados = array_filter($camposAtualizaveis, function ($valor) {
			return $valor !== null;
		});

        if (empty($camposEnviados)) {
            http_response_code(400);
            echo json_encode(["mensagem" => "Nenhum campo válido foi enviado para atualização"]);
            exit;
        }

        // Monta a query dinamicamente
        $setQuery = implode(', ', array_map(function ($key) {
			return "$key = :$key";
		}, array_keys($camposEnviados)));
        $query = "UPDATE Titulo SET $setQuery WHERE IdTitulo = :idTitulo";

        $stmt = $pdo->prepare($query);
        $camposEnviados['idTitulo'] = $idTitulo;
        $stmt->execute($camposEnviados);

        // Retorna o título atualizado
        $stmt = $pdo->prepare("SELECT * FROM Titulo WHERE IdTitulo = :idTitulo");
        $stmt->execute([':idTitulo' => $idTitulo]);
        $tituloAtualizado = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tituloAtualizado) {
            http_response_code(404);
            echo json_encode(["mensagem" => "Título não encontrado"]);
            exit;
        }

        echo json_encode([
            "mensagem" => "Título atualizado com sucesso",
            "idTitulo" => $idTitulo,
            "tituloAtualizado" => $tituloAtualizado
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
