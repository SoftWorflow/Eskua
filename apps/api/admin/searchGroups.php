<?php

require_once(__DIR__ . '/../../../backend/logic/group/GroupLogicFacade.php');
header('Content-Type: application/json');


$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

$teacher = $data['teacher'] ?? '';

$groupLogic = GroupLogicFacade::getInstance()->getIGroupLogic();

$groups = $groupLogic->searchGroupsByTeacherNameAdmin($teacher);

echo json_encode($groups);

?>