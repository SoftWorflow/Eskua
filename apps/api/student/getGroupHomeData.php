<?php

require_once(__DIR__ . "/../../../backend/DTO/Users/User.php");
require_once(__DIR__ . "/../../../backend/logic/user/UserLogicFacade.php");
require_once(__DIR__ . "/../../../backend/logic/group/GroupLogicFacade.php");
header('Content-Type: application/json');

$userLogic = UserLogicFacade::getInstance()->getIUserLogic();
$groupLogic = GroupLogicFacade::getInstance()->getIGroupLogic();

$group = $userLogic->getStudentGroup();

$teacherData = $userLogic->getUserById($group['teacher'])[1];

$teacher['displayName'] = $teacherData->getDisplayName();
$teacher['profilePicture'] = $teacherData->getProfilePictureUrl();

$groupMembers = $groupLogic->getGroupMembers($group['id']);

if ($groupMembers['ok']) {
    echo json_encode([
        'ok' => true,
        'teacher' => $teacher,
        'group' => $group,
        'groupMembers' => $groupMembers['members']
    ]);
}

?>