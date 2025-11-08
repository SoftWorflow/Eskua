<?php

require_once(__DIR__ . "/../../../backend/DTO/PublicMaterial.php");

require_once(__DIR__ . "/../../../backend/logic/material/MaterialLogicFacade.php");
header('Content-Type: application/json');

$materialTitle = $_POST['title'] ?? '';
$materialDescription = $_POST['description'] ?? '';

$material = new PublicMaterial($materialTitle, $materialDescription);

$materialLogic = MaterialLogicFacade::getInstance()->getIMaterialLogic();

$file = $_FILES['file'] ?? null;

$materialResponse = $materialLogic->uploadMaterial($material, $file);

echo json_encode($materialResponse);

?>