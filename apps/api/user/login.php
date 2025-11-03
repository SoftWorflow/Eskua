<?php
require_once(__DIR__ . "/../../../backend/DTO/Users/User.php");
require_once(__DIR__ . "/../../../backend/logic/user/UserLogicFacade.php");
require_once('token.php');

require(__DIR__ . '/../../../backend/vendor/autoload.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$username = $input['username'] ?? '';
$password = $input['password'] ?? '';

if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Usuario y contraseña necesarios', 'ok' => false]);
    exit;
}

$userLogic = UserLogicFacade::getInstance()->getIUserLogic();

$dbUser = $userLogic->getUserByUsername($username);
if (!$dbUser) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuario o contraseña incorrectos', 'ok' => false]);
    exit;
}

$user = $dbUser[1];

if (!password_verify($password, $user->getPassword())) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuario o contraseña incorrectos', 'ok' => false]);
    exit;
}

$token = generateToken($user);

if ($token === null) exit;

echo json_encode($token);
?>