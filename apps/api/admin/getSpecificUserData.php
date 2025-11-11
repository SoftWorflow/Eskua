<?php

require_once(__DIR__ . "/../../../backend/logic/user/UserLogicFacade.php");
header('Content-Type: application/json');

$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

$userId = $data['id'] ?? '';

$userLogic = UserLogicFacade::getInstance()->getIUserLogic();

$userData = $userLogic->getSpecificUserData($userId);

echo json_encode($userData);

?>