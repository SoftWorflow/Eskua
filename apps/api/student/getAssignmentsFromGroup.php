<?php

require_once(__DIR__ . "/../middleware/auth.php");

require_once(__DIR__ . "/../../../backend/DTO/Users/User.php");
require_once(__DIR__ . "/../../../backend/logic/user/UserLogicFacade.php");
header('Content-Type: application/json');

$auth = new AuthMiddleware();
$auth::authorize(['student']);

$user = $auth::authenticate();
$userId = $user['user_id'];

$userLogic = UserLogicFacade::getInstance()->getIUserLogic();

$userGroupId = $userLogic->getStudentGroup($userId)['id'];

$assignments = $userLogic->getAssignmentsFromGroup($userGroupId);

echo json_encode(['ok' => true, $assignments]);

?>