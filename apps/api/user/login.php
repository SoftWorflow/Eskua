<?php
require_once(__DIR__ . "/../../../backend/DTO/Users/User.php");
require_once(__DIR__ . "/../../../backend/logic/user/UserLogicFacade.php");
require_once('token.php');

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$username = $input['username'] ?? '';
$password = $input['password'] ?? '';

$userLogic = UserLogicFacade::getInstance()->getIUserLogic();

$login = $userLogic->login($username, $password);

echo json_encode($login);
?>