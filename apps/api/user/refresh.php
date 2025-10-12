<?php

require_once('token.php');
header('Content-Type: application/json');

$newToken = refreshToken();

if ($newToken === null) {
    echo json_encode(['error' => 'Refresh token expirado o invalido', 'ok' => false]);
    exit;
}

$newToken['ok'] = true;
echo json_encode($newToken);

?>