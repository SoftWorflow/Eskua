<?php

require_once(__DIR__ . "/../../../backend/DTO/GroupAssignment.php");

require_once(__DIR__ . "/../../../backend/logic/assignment/AssignmentLogicFacade.php");
header('Content-Type: application/json');

$name = $_POST['title'] ?? '';
$descrption = $_POST['description'] ?? '';
$maxScore = $_POST['maxScore'] ?? '';
$dueDateRaw = $_POST['dueDate'] ?? '';
$groupId = $_POST['groupId'] ?? '';
$taskId = $_POST['taskId'] ?? '';
$hasChangedFileRaw = $_POST['hasChangedFile'] ?? false;
$filePath = $_POST['filePath'] ?? '';
$hasChangedFile = filter_var($hasChangedFileRaw, FILTER_VALIDATE_BOOLEAN);

$dueDate = DateTime::createFromFormat('d-m-Y', $dueDateRaw);

$assignment = new GroupAssignment($name, $descrption, $maxScore, $groupId, $dueDate);

$assignmentLogic = AssignmentLogicFacade::getInstance()->getIAssignmentLogic();

$result = $assignmentLogic->modifyAssignment(intval($taskId), $assignment, $hasChangedFile, $_FILES['file'] ?? null, $filePath);

echo json_encode($result);

?>