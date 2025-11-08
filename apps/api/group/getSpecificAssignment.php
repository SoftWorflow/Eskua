<?php

require_once(__DIR__ . "/../middleware/auth.php");

require_once(__DIR__ . "/../../../backend/logic/group/GroupLogicFacade.php");
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$auth = new AuthMiddleware();
$auth::authorize(['student', 'teacher']);

if (!$auth::authenticate()) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'No tiene accesso a este endpoint']);
    exit;
}

$assignmentId = $input['taskId'] ?? '';

if (empty($assignmentId)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'No se recibió el identificador de la tarea']);
    exit;
}

$groupLogic = GroupLogicFacade::getInstance()->getIGroupLogic();

$assignment = $groupLogic->getAssignment($assignmentId);

if ($assignment === null) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'No se encontró la tarea']);
    exit;
}

$dueDate = new DateTime($assignment['dueDate']);

$assignment['dueDate'] = $dueDate->format("d/m/Y");

$storageName = $assignment['storageName'];
$createdDate = new DateTime($assignment['createdAt']);

$year = $createdDate->format('Y');
$month = $createdDate->format('m');

$filePath = 'uploads/'.$year.'/'.$month.'/'.$storageName;

$assignment['filePath'] = $filePath;

echo json_encode(['ok' => true, 'task' => $assignment]);

?>