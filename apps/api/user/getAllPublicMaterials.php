<?php

require_once(__DIR__ . "/../middleware/auth.php");

require_once(__DIR__ . "/../../../backend/logic/material/MaterialLogicFacade.php");
header('Content-Type: application/json');

$auth = new AuthMiddleware();

if (!$auth::authenticate()) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'No tienes permiso']);
    exit;
}

$materialLogic = MaterialLogicFacade::getInstance()->getIMaterialLogic();
$materials = $materialLogic->getAllMaterials();

if ($materials !== null && count($materials) !== 0) {
    foreach ($materials as &$material) {
        if (isset($material['createdDate']) && !empty($material['createdDate'])) {
            $date = DateTime::createFromFormat('Y-m-d H:i:s', $material['createdDate']);
            if ($date) {
                $material['createdDate'] = $date->format('d-m-Y');
            }
        }
    }
    unset($material);
}

echo json_encode(['ok' => true, 'materials' => $materials]);

?>