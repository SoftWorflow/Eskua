<?php

require_once(__DIR__ . "/../../../backend/DTO/Users/User.php");
require_once(__DIR__ . "/../../../backend/logic/user/UserLogicFacade.php");
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$userLogic = UserLogicFacade::getInstance()->getIUserLogic();

$groupId = $input['id'];

$assignments = $userLogic->getAssignmentsFromGroup($groupId);

$currentDate = new DateTime('now');

echo json_encode(['ok' => true, $assignments]);

?>