<?php

// Função para validar o token e verificar o nível de acesso
function validarToken($nivelNecessario = null) {
    $headers = getallheaders();

    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(["mensagem" => "Token não fornecido"]);
        exit;
    }

    $token = str_replace('Bearer ', '', $headers['Authorization']);
    $decodedToken = json_decode(base64_decode($token), true);

    if (!$decodedToken || $decodedToken['exp'] < time()) {
        http_response_code(401);
        echo json_encode(["mensagem" => "Token inválido ou expirado"]);
        exit;
    }

    if ($nivelNecessario !== null && (int)$decodedToken['nivelAcesso'] !== $nivelNecessario) {
        http_response_code(403);
        echo json_encode(["mensagem" => "Permissão negada"]);
        exit;
    }

    return $decodedToken;
}

?>
