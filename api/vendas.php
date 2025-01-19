<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/functions.php';

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

try {
    $pdo = getConnection();

    if ($method === 'GET') {
        // Endpoint: GET /vendas/{idVenda}
        validarToken(); // Qualquer usuário logado pode acessar

        $idVenda = isset($_GET['idVenda']) ? $_GET['idVenda'] : null;

        if (!$idVenda || !is_numeric($idVenda)) {
            http_response_code(400);
            echo json_encode(["mensagem" => "ID da venda inválido"]);
            exit;
        }

		// Obter os detalhes da venda
        $stmt = $pdo->prepare(
            "SELECT V.*, C.IdCliente, C.Nome AS NomeCliente, C.Email
             FROM Venda V
             JOIN Cliente C ON V.IdCliente = C.IdCliente
             WHERE V.IdVenda = :idVenda"
        );
        $stmt->execute([':idVenda' => $idVenda]);
        $venda = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$venda) {
            http_response_code(404);
            echo json_encode(["mensagem" => "Venda não encontrada"]);
            exit;
        }

		// Obter os itens da venda
        $stmt = $pdo->prepare(
            "SELECT VI.IdProduto, P.Nome, VI.Quantidade, VI.ValorUnitario
             FROM Venda_Item VI
             JOIN Produto P ON VI.IdProduto = P.IdProduto
             WHERE VI.IdVenda = :idVenda"
        );
        $stmt->execute([':idVenda' => $idVenda]);
        $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $venda['cliente'] = [
            "idCliente" => $venda['IdCliente'],
            "nome" => $venda['NomeCliente'],
            "email" => $venda['Email']
        ];
        unset($venda['IdCliente'], $venda['NomeCliente'], $venda['Email']);

        $venda['itens'] = $itens;

        echo json_encode($venda);

    } elseif ($method === 'POST') {
        // Endpoint: POST /vendas
        $usuario = validarToken(); // Qualquer usuário logado pode acessar

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['idCliente'], $data['itens']) || !is_array($data['itens'])) {
            http_response_code(400);
            echo json_encode(["mensagem" => "Dados da venda incompletos"]);
            exit;
        }

        $idUsuario = $usuario['idUsuario']; // ID do usuário obtido do token

        $pdo->beginTransaction();

        // Valida se o cliente existe
        $stmt = $pdo->prepare("SELECT * FROM Cliente WHERE IdCliente = :idCliente");
        $stmt->execute([':idCliente' => $data['idCliente']]);
        if (!$stmt->fetch()) {
            $pdo->rollBack();
            http_response_code(400);
            echo json_encode(["mensagem" => "Cliente não encontrado"]);
            exit;
        }

        // Valida se os produtos existem e têm estoque suficiente
        foreach ($data['itens'] as $item) {
            $stmt = $pdo->prepare("SELECT Estoque FROM Produto WHERE IdProduto = :idProduto");
            $stmt->execute([':idProduto' => $item['idProduto']]);
            $produto = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$produto) {
                $pdo->rollBack();
                http_response_code(400);
                echo json_encode(["mensagem" => "Produto não encontrado", "idProduto" => $item['idProduto']]);
                exit;
            }

            if ($produto['Estoque'] < $item['quantidade']) {
                $pdo->rollBack();
                http_response_code(400);
                echo json_encode(["mensagem" => "Estoque insuficiente", "idProduto" => $item['idProduto']]);
                exit;
            }
        }
		
		// Cálculo do valor total da venda
        $valorTotal = 0;
        foreach ($data['itens'] as $item) {
            $valorTotal += $item['quantidade'] * $item['valorUnitario'];
        }

        // Validação das parcelas (se fornecidas)
        $titulos = isset($data['titulo']) ? $data['titulo'] : null;
        if ($titulos) {
            $valorSomaParcelas = 0;
            foreach ($titulos as $titulo) {
                if (!isset($titulo['valorParcela'], $titulo['dataVencimento'])) {
                    http_response_code(400);
                    echo json_encode(["mensagem" => "Parcela incompleta"]);
                    exit;
                }
                $valorSomaParcelas += $titulo['valorParcela'];
            }

            if (abs($valorSomaParcelas - $valorTotal) > 0.01) {
                http_response_code(400);
                echo json_encode(["mensagem" => "Soma das parcelas não corresponde ao valor total da venda"]);
                exit;
            }
        }

		$dataAtual = date('Y-m-d H:i:s');

        // Inserir a venda
        $stmt = $pdo->prepare(
            "INSERT INTO Venda (IdUsuario, IdCliente, DataVenda, ValorTotal)
             VALUES (:idUsuario, :idCliente, :dataVenda, :valorTotal)"
        );
        $stmt->execute([
            ':idUsuario' => $idUsuario,
            ':idCliente' => $data['idCliente'],
            ':dataVenda' => $dataAtual,
            ':valorTotal' => $valorTotal
        ]);

        $idVenda = $pdo->lastInsertId();

        // Inserir os itens e atualizar o estoque
        $valorTotal = 0;
        $stmt = $pdo->prepare(
            "INSERT INTO Venda_Item (IdVenda, IdProduto, Quantidade, ValorUnitario)
             VALUES (:idVenda, :idProduto, :quantidade, :valorUnitario)"
        );
        foreach ($data['itens'] as $item) {
            $stmt->execute([
                ':idVenda' => $idVenda,
                ':idProduto' => $item['idProduto'],
                ':quantidade' => $item['quantidade'],
                ':valorUnitario' => $item['valorUnitario']
            ]);
            $valorTotal += $item['quantidade'] * $item['valorUnitario'];

            // Atualizar o estoque do produto
            $stmtEstoque = $pdo->prepare(
                "UPDATE Produto SET Estoque = Estoque - :quantidade WHERE IdProduto = :idProduto"
            );
            $stmtEstoque->execute([
                ':quantidade' => $item['quantidade'],
                ':idProduto' => $item['idProduto']
            ]);
        }

		// Criar títulos
        $stmtTitulo = $pdo->prepare(
            "INSERT INTO Titulo (IdVenda, Parcela, ValorTitulo, ValorPago, DataEmissao, DataVencimento, DataPago)
             VALUES (:idVenda, :parcela, :valorTitulo, NULL, :DataEmissao, :dataVencimento, NULL)"
        );

        if ($titulos) {
            foreach ($titulos as $index => $titulo) {
                $stmtTitulo->execute([
                    ':idVenda' => $idVenda,
                    ':parcela' => $index + 1,
                    ':valorTitulo' => $titulo['valorParcela'],
                    ':DataEmissao' => $dataAtual,
                    ':dataVencimento' => $titulo['dataVencimento']
                ]);
            }
        } else {
            // Criar uma única parcela se nenhuma foi fornecida
            $stmtTitulo->execute([
                ':idVenda' => $idVenda,
                ':parcela' => 1,
                ':valorTitulo' => $valorTotal,
                ':DataEmissao' => $dataAtual,
                ':dataVencimento' => date('Y-m-d')
            ]);
        }

        $pdo->commit();

        echo json_encode([
            "mensagem" => "Venda registrada com sucesso",
            "idVenda" => $idVenda,
            "valorTotal" => $valorTotal
        ]);
    } else {
        http_response_code(405);
        echo json_encode(["mensagem" => "Método não permitido"]);
    }
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(["mensagem" => "Erro no servidor", "erro" => $e->getMessage()]);
}
?>
