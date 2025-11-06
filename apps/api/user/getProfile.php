<?php

require_once(__DIR__ . "/../middleware/auth.php");

require_once(__DIR__ . "/../../../backend/DTO/Users/User.php");
require_once(__DIR__ . "/../../../backend/logic/user/UserLogicFacade.php");

$auth = new AuthMiddleware();
$user = $auth::authenticate();

if ($user === null) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Acceso denegado']);
    exit;
}

$userId = $user['user_id'];

$userLogic = UserLogicFacade::getInstance()->getIUserLogic();
$userData = $userLogic->getUserById($userId);

$user = $userData[1];

$response = [
    'displayName' => $user->getDisplayName(),
    'email' => $user->getEmail(),
    'role' => $user->getUserRole(),
    'profilePic' => $user->getProfilePictureUrl()
];

echo json_encode(['ok' => true, 'profile' => $response]);

?>