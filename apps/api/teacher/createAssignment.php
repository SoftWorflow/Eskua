<?php

require_once(__DIR__ . "/../../../backend/DTO/GroupAssignment.php");

require_once(__DIR__ . "/../../../backend/logic/assignment/AssignmentLogicFacade.php");
header('Content-Type: application/json');

$name = $_POST['title'] ?? '';
$descrption = $_POST['description'] ?? '';
$maxScore = $_POST['maxScore'] ?? '';
$dueDateRaw = $_POST['dueDate'] ?? '';
$groupId = $_POST['groupId'] ?? '';

$dueDate = DateTime::createFromFormat('d-m-Y', $dueDateRaw);

$assignment = new GroupAssignment($name, $descrption, $maxScore, $groupId, $dueDate);

$assignmentLogic = AssignmentLogicFacade::getInstance()->getIAssignmentLogic();

$response = $assignmentLogic->createAssignment($assignment, isset($_FILES['file']) ? $_FILES['file'] : null);

echo json_encode($response);

?>