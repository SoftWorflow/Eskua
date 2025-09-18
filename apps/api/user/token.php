<?php
require_once(__DIR__ . "/../../../backend/DTO/Users/User.php");
require_once(__DIR__ . "/../../../backend/logic/user/UserLogicFacade.php");

function generateToken(User $user) : bool {

    $userLogic = UserLogicFacade::getInstance()->getIUserLogic();

    $token = $userLogic->generateToken($user);
    
    if ($token === null) {
        http_response_code(500);
        echo json_encode(['error' => 'Error while generating token', 'ok' => false]);
        return false;
    } else {
        $token['ok'] = true;
        echo json_encode($token);
        return true;
    }

}

function refreshToken() : bool {
    $userLogic = UserLogicFacade::getInstance()->getIUserLogic();

    $newToken = $userLogic->refreshToken();

    if ($newToken === null) {
        http_response_code(500);
        echo json_encode(['error' => 'Error while creating another token', 'ok' => false]);
        return false;
    } else {
        $newToken['ok'] = true;
        echo json_encode($newToken);
        return true;
    }
}

?>