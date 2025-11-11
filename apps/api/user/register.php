<?php

require_once(__DIR__ . "/../../../backend/logic/user/UserLogicFacade.php");
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$username = $input['username'] ?? '';
$displayName = $input['displayName'] ?? '';
$email = $input['email'] ?? '';
$password = $input['password'] ?? '';
$confirmPassword = $input['confirmPassword'] ?? '';
$userRole = $input['userType'] ?? '';
$groupCode = $input['groupCode'] ?? '';

$userLogic = UserLogicFacade::getInstance()->getIUserLogic();

$response = $userLogic->register($username, $email, $displayName, $password, $confirmPassword, $userRole, $groupCode);

echo json_encode($response);

?>