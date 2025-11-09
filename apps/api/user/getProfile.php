<?php

require_once(__DIR__ . "/../../../backend/DTO/Users/User.php");
require_once(__DIR__ . "/../../../backend/logic/user/UserLogicFacade.php");
header('Content-Type: application/json');

$userLogic = UserLogicFacade::getInstance()->getIUserLogic();
$userData = $userLogic->getUserById(null);

$user = $userData[1];

$response = [
    'displayName' => $user->getDisplayName(),
    'email' => $user->getEmail(),
    'role' => $user->getUserRole(),
    'profilePic' => $user->getProfilePictureUrl()
];

echo json_encode(['ok' => true, 'profile' => $response]);

?>