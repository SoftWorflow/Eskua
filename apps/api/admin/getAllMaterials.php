<?php

require_once(__DIR__ . "/../../../backend/logic/material/MaterialLogicFacade.php");
header('Content-Type: application/json');

$materialLogic = MaterialLogicFacade::getInstance()->getIMaterialLogic();

$materialsData = $materialLogic->getAllMaterialsAdmin();

echo json_encode($materialsData);

?>