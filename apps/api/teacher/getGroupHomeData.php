<?php

require_once(__DIR__ . "/../middleware/auth.php");

require_once(__DIR__ . "/../../../backend/DTO/Users/User.php");
require_once(__DIR__ . "/../../../backend/logic/user/UserLogicFacade.php");
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$auth = new AuthMiddleware();
$auth::authorize(['teacher']);

$user = $auth::authenticate();
$userId = $user['user_id'];

$groupId = $input['id'];

$userLogic = UserLogicFacade::getInstance()->getIUserLogic();
$groupMembers = $userLogic->getGroupMembers($groupId);

$teacherData = $userLogic->getUserById($userId)[1];
$teacher['displayName'] = $teacherData->getDisplayName();
$teacher['profilePicture'] = $teacherData->getProfilePictureUrl();

$group = $userLogic->getGroup($groupId);

if ($group === null) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => "Error obteniendo grupo"]);
    exit;
}

echo json_encode([
    'ok' => true,
    'teacher' => $teacher,
    'group' => $group,
    'groupMembers' => $groupMembers
]);

?>