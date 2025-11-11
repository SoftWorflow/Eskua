<?php

require_once(__DIR__ . "/../../../backend/DTO/Users/User.php");
require_once(__DIR__ . "/../../../backend/logic/user/UserLogicFacade.php");
header('Content-Type: application/json');

$userLogic = UserLogicFacade::getInstance()->getIUserLogic();

$userGroupId = $userLogic->getStudentGroup()['id'];

$assignments = $userLogic->getAssignmentsFromGroup($userGroupId);

echo json_encode($assignments);

?>