<?php

require_once(__DIR__ . "/../middleware/auth.php");

require_once(__DIR__ . "/../../../backend/logic/material/MaterialLogicFacade.php");
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$auth = new AuthMiddleware();

if (!$auth::authenticate()) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'No tienes permiso']);
    exit;
}

$materialId = $input['materialId'] ?? '';

if (empty($materialId)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'No se ricibió el identificador del material']);
    exit;
}

$materialLogic = MaterialLogicFacade::getInstance()->getIMaterialLogic();

$material = $materialLogic->getMaterial($materialId);

if ($material !== null) {
    if (isset($material['createdDate']) && !empty($material['createdDate'])) {
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $material['createdDate']);
        if ($date) {
            $material['createdDate'] = $date->format('d-m-Y');
        }
    }
}

$createdDate = new DateTime($material['createdDate']);
$storageName = $material['storageName'];

$year = $createdDate->format('Y');
$month = $createdDate->format('m');
$day = $createdDate->format('d');

$filePath = '/uploads/'.$year.'/'.$month.'/'.$storageName;

echo json_encode(['ok' => true, 'material' => $material, 'filePath' => $filePath]);

?>