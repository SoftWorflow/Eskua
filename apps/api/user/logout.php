<?php
require_once(__DIR__ . "/../../../backend/logic/user/UserLogicFacade.php");
header('Content-Type: application/json');

if (!isset($_COOKIE['refresh_token'])) {
    error_log("Logout: No refresh token cookie found");
    echo json_encode(['ok' => true, 'message' => 'No active session']);
    exit;
}

$refreshToken = $_COOKIE['refresh_token'];
$userLogic = UserLogicFacade::getInstance()->getIUserLogic();

// Revoke token on the DB
$revoked = $userLogic->revokeRefreshToken($refreshToken);

if (!$revoked) {
    error_log("Logout: Failed to revoke token in database");
}

// Borrar la cookie
$cookieDeleted = setcookie('refresh_token', '', [
    'expires' => time() - 3600,
    'path' => '/',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Strict'
]);

if (!$cookieDeleted) {
    error_log("Logout: Failed to delete cookie");
}

error_log("Logout: Token revoked=" . ($revoked ? 'yes' : 'no') . ", Cookie deleted=" . ($cookieDeleted ? 'yes' : 'no'));

echo json_encode([
    'ok' => true, 
    'token_revoked' => $revoked,
    'cookie_deleted' => $cookieDeleted
]);
?>