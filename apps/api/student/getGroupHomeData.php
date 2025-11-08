<?php

require_once(__DIR__ . "/../middleware/auth.php");

require_once(__DIR__ . "/../../../backend/DTO/Users/User.php");
require_once(__DIR__ . "/../../../backend/logic/user/UserLogicFacade.php");
header('Content-Type: application/json');

$auth = new AuthMiddleware();
$auth::authorize(['student']);

$authUser = $auth::authenticate();
$authUserId = $authUser['user_id'];

$userLogic = UserLogicFacade::getInstance()->getIUserLogic();
$group = $userLogic->getStudentGroup();

$teacherData = $userLogic->getUserById($group['teacher'])[1];

$teacher['displayName'] = $teacherData->getDisplayName();
$teacher['profilePicture'] = $teacherData->getProfilePictureUrl();

$groupMembers = $userLogic->getGroupMembers($group['id']);

if ($groupMembers === null) {
    http_response_code(500);
    echo json_encode(['ok'=> false, 'error' => 'No se pudieron obtener los miembros del grupo']);
    exit;
}

echo json_encode([
    'ok' => true,
    'teacher' => $teacher,
    'group' => $group,
    'groupMembers' => $groupMembers
]);

?>