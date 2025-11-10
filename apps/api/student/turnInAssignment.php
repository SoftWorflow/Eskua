<?php

require_once(__DIR__ . "/../../../backend/logic/assignment/AssignmentLogicFacade.php");
header('Content-Type: application/json');

$text = $_POST['text'];
$file = $_FILES['file'] ?? null;
$assignmentId = $_POST['taskId'];

$assignmentLogic = AssignmentLogicFacade::getInstance()->getIAssignmentLogic();

$response = $assignmentLogic->turnInAssignment($assignmentId, $text, $file);

echo json_encode($response);

?>