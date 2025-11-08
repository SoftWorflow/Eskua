<?php
require_once(__DIR__ . "/../middleware/auth.php");

require_once(__DIR__ . "/../../../backend/logic/group/GroupLogicFacade.php");
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$auth = new AuthMiddleware();
$auth::authorize(['teacher']);

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

if (!$groupLogic->deactivateAssignment($assignmentId)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Hubo un error al desactivar la tarea']);
    exit;
}

echo json_encode(['ok' => true, 'message' => 'El material se ha desactivado correctamente']);

?>