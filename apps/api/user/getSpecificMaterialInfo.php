<?php

require_once(__DIR__ . "/../../../backend/logic/material/MaterialLogicFacade.php");
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$materialId = $input['materialId'] ?? '';

$materialLogic = MaterialLogicFacade::getInstance()->getIMaterialLogic();

$material = $materialLogic->getMaterial($materialId);

$createdDate = new DateTime($material['createdDate']);
$storageName = $material['storageName'];

$year = $createdDate->format('Y');
$month = $createdDate->format('m');
$day = $createdDate->format('d');

$filePath = '/uploads/'.$year.'/'.$month.'/'.$storageName;

echo json_encode(['ok' => true, 'material' => $material, 'filePath' => $filePath]);

?>