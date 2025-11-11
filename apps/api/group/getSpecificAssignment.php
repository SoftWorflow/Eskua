<?php

require_once(__DIR__ . "/../../../backend/logic/assignment/AssignmentLogicFacade.php");
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$assignmentId = $input['taskId'] ?? '';

$assignmentLogic = AssignmentLogicFacade::getInstance()->getIAssignmentLogic();

$assignment = $assignmentLogic->getSpecificAssignment($assignmentId);

echo json_encode($assignment);

?>