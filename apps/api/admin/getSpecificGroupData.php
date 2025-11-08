<?php

require_once(__DIR__ . '/../../../backend/logic/group/GroupLogicFacade.php');
header('Content-Type: application/json');

$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

$groupId = $data['id'] ?? '';

$groupLogic = GroupLogicFacade::getInstance()->getIGroupLogic();

$groupData = $groupLogic->getSpecificGroupDataAdmin($groupId);

echo json_encode($groupData);

?>