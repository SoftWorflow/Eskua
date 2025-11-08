<?php

require_once(__DIR__ . "/../../../backend/logic/user/UserLogicFacade.php");
require_once(__DIR__ . "/../../../backend/logic/material/MaterialLogicFacade.php");
require_once(__DIR__ . "/../../../backend/logic/group/GroupLogicFacade.php");
require_once(__DIR__ . "/../../../backend/logic/assignment/AssignmentLogicFacade.php");
header('Content-Type: application/json');

$userLogic = UserLogicFacade::getInstance()->getIUserLogic();
$groupLogic = GroupLogicFacade::getInstance()->getIGroupLogic();
$materialLogic = MaterialLogicFacade::getInstance()->getIMaterialLogic();
$assignmentLogic = AssignmentLogicFacade::getInstance()->getIAssignmentLogic();

$response = [];

// USERS
$response = array_merge($response, $userLogic->getAllUsersCountAdmin());

// PUBLIC MATERIALS
$publicMaterialsCount = $materialLogic->getMaterialsCountAdmin();
$response['publicMaterialsCount'] = $publicMaterialsCount;

$recentMaterials = $materialLogic->getRecentMaterials();
$response['recentMaterials'] = $recentMaterials;

// GROUPS
$groupsCount = $groupLogic->getAllGroupsCountAdmin();
$response['groupsCount'] = $groupsCount;

// ASSIGNMENTS
$assignmentsCount = $assignmentLogic->getAllAssignmentsCountAdmin();
$response['assignmentsCount'] = $assignmentsCount;

// TUREND IN ASSIGNMENTS
$turnedInAssignmentsCount = $assignmentLogic->getAllTurnedInAssignmentsCountAdmin();
$response['turnedInAssignmentsCount'] = $turnedInAssignmentsCount;

foreach ($response as $key => $value) {
    if ($value == 0) {
        $response[$key] = "N/A";
    }
}

echo json_encode($response);

?>