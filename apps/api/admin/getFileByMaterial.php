<?php

require_once(__DIR__ . "/../../../backend/logic/material/MaterialLogicFacade.php");
header('Content-Type: application/json');

$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

$materialId = $data['id'] ?? '';

$materialLogic = MaterialLogicFacade::getInstance()->getIMaterialLogic();

$response = $materialLogic->getFileByMaterialAdmin($materialId);

echo json_encode($response);

?>