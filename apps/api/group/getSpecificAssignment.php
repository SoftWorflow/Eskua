<?php

require_once(__DIR__ . "/../../../backend/logic/group/GroupLogicFacade.php");
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$assignmentId = $input['taskId'] ?? '';

$groupLogic = GroupLogicFacade::getInstance()->getIGroupLogic();

$assignment = $groupLogic->getAssignment($assignmentId);

echo json_encode($assignment);

?>