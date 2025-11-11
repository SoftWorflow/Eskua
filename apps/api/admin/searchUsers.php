<?php

require_once(__DIR__ . "/../../../backend/logic/user/UserLogicFacade.php");
header('Content-Type: application/json');

$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

$username = $data['username'] ?? '';

$userLogic = UserLogicFacade::getInstance()->getIUserLogic();

$usersData = $userLogic->searchUsers($username);

echo json_encode($usersData);

?>