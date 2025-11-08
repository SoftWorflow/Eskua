<?php

require_once(__DIR__ . "/../../../backend/logic/material/MaterialLogicFacade.php");
header('Content-Type: application/json');

$auth = new AuthMiddleware();
$auth->authorize(['admin']);

$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (!$data && !isset($data['title'])) exit;

$materialTitle = $data['title'] ?? '';

$materialLogic = MaterialLogicFacade::getInstance()->getIMaterialLogic();

$materialsData = $materialLogic->searchMaterial($materialTitle);

echo json_encode($materialsData);

?>