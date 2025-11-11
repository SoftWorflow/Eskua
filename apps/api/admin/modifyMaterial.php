<?php

require_once(__DIR__ . "/../../../backend/DTO/PublicMaterial.php");
require_once(__DIR__ . "/../../../backend/DTO/File.php");

require_once(__DIR__ . "/../../../backend/logic/file/FileLogicFacade.php");
require_once(__DIR__ . "/../../../backend/logic/material/MaterialLogicFacade.php");
header('Content-Type: application/json');

$materialId = $_POST['id'];
$materialTitle = $_POST['title'] ?? '';
$materialDescription = $_POST['description'] ?? '';
$hasChangedFileRaw = $_POST['hasChangedFile'] ?? false;
$filePath = $_POST['filePath'] ?? '';
$hasChangedFile = filter_var($hasChangedFileRaw, FILTER_VALIDATE_BOOLEAN);

$materialLogic = MaterialLogicFacade::getInstance()->getIMaterialLogic();

$response = $materialLogic->modifyMaterial($materialId, $materialTitle, $materialDescription, $hasChangedFile, $_FILES['file'] ?? null, $filePath);

echo json_encode($response);

?>