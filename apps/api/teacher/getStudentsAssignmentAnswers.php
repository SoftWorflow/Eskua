<?php

require_once(__DIR__ . "/../../../backend/logic/assignment/AssignmentLogicFacade.php");
header('Content-Type: application/json');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

$assignmentId = $data['taskId'];

$assignmentLogic = AssignmentLogicFacade::getInstance()->getIAssignmentLogic();

$assignments = $assignmentLogic->getTurnedInAssignmentsFromAssignment($assignmentId);

echo json_encode($assignments);

?>