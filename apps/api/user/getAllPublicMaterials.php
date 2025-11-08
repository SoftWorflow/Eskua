<?php

require_once(__DIR__ . "/../../../backend/logic/material/MaterialLogicFacade.php");
header('Content-Type: application/json');

$materialLogic = MaterialLogicFacade::getInstance()->getIMaterialLogic();
$materials = $materialLogic->getAllMaterials();

if ($materials !== null) {
    echo json_encode(['ok' => true, 'materials' => $materials]);
} else {
    echo json_encode(['ok' => false, 'error' => 'Hubo un error al obtener los materiales públicos']);
}

?>