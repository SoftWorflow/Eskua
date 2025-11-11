<?php

require_once(__DIR__ . "/../../../backend/DTO/Users/User.php");
require_once(__DIR__ . "/../../../backend/logic/user/UserLogicFacade.php");
header('Content-Type: application/json');

$auth = new AuthMiddleware();
$auth::authorize(['teacher']);

$authUser = $auth::authenticate();
$authUserId = $authUser['user_id'];

$userLogic = UserLogicFacade::getInstance()->getIUserLogic();
$groups = $userLogic->getTeacherGroups($authUserId);

echo json_encode($groups);

?>