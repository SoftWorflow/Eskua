<?php
require_once(__DIR__ . "/../../../backend/DTO/Users/User.php");
require_once(__DIR__ . "/../../../backend/logic/user/UserLogicFacade.php");

require(__DIR__ . '/../../../backend/vendor/autoload.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function generateToken(User $user) : ?array {

    $userLogic = UserLogicFacade::getInstance()->getIUserLogic();

    $token = $userLogic->generateToken($user);
    
    if ($token === null) {
        http_response_code(500);
        return null;
    } else {
        $token['ok'] = true;
        return $token;
    }

}

function refreshToken() : ?array {
    $userLogic = UserLogicFacade::getInstance()->getIUserLogic();

    $newToken = $userLogic->refreshToken();

    if ($newToken === null) {
        http_response_code(500);
        return null;
    } else {
        $newToken['ok'] = true;
        return $newToken;
    }
}

function getUserRoleFromToken(string $jwtToken) : string {
    $secretKey = getenv('CLIENT_TOKEN_SECRET');
    $algorithm = 'HS256';

    try {
        $decoded = JWT::decode($jwtToken, new Key($secretKey, $algorithm));

        $decodedArray = (array) $decoded;

        if (isset($decodedArray['role'])) {
            return $decodedArray['role'];
        }

        return '';

    } catch (\Firebase\JWT\ExpiredException $e) {
        return null;
    } catch (\Exception $e) {
        return null;
    }
}
?>