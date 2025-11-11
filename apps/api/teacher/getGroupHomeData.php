<?php

require_once(__DIR__ . "/../../../backend/DTO/Users/User.php");
require_once(__DIR__ . "/../../../backend/logic/user/UserLogicFacade.php");
require_once(__DIR__ . "/../../../backend/logic/group/GroupLogicFacade.php");
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$groupId = $input['id'];

$userLogic = UserLogicFacade::getInstance()->getIUserLogic();
$groupLogic = GroupLogicFacade::getInstance()->getIGroupLogic();

$groupMembers = $groupLogic->getGroupMembers($groupId);

$teacherData = $userLogic->getUserById()[1];
$teacher['displayName'] = $teacherData->getDisplayName();
$teacher['profilePicture'] = $teacherData->getProfilePictureUrl();

$group = $groupLogic->getGroup($groupId);

if (!$group['ok']) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => "Error obteniendo grupo"]);
    exit;
}

echo json_encode([
    'ok' => true,
    'teacher' => $teacher,
    'group' => $group['group'],
    'groupMembers' => $groupMembers['members']
]);

?>