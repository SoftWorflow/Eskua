<?php

require_once(__DIR__ . "/../../../backend/logic/assignment/AssignmentLogicFacade.php");
header('Content-Type: application/json');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

$studentAnswerId = $data['answerId'];

$assignmentLogic = AssignmentLogicFacade::getInstance()->getIAssignmentLogic();

$assignment = $assignmentLogic->getSpecificStudenAnswerById($studentAnswerId);

echo json_encode($assignment);

?>