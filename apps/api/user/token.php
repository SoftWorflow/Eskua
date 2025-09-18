<?php
require_once(__DIR__ . "/../../../backend/DTO/Users/User.php");
require_once(__DIR__ . "/../../../backend/logic/user/UserLogicFacade.php");

function generateToken(User $user) {

    $userLogic = UserLogicFacade::getInstance()->getIUserLogic();

    $token = $userLogic->generateToken($user);

    if ($token === null) {
        http_response_code(500);
        echo json_encode(['error' => 'Error while generating token']);
        exit;
    } else {
        echo json_encode($token);
    }

}
?>