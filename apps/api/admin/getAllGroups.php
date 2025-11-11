<?php


require_once(__DIR__ . "/../../../backend/logic/group/GroupLogicFacade.php");
header('Content-Type: application/json');

$groupLogic = GroupLogicFacade::getInstance()->getIGroupLogic();

$groupsData = $groupLogic->getAllGroupsAdmin();

echo json_encode($groupsData);

?>