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

if ($groups === null) {
    http_response_code(500);
    echo json_encode(['ok'=> false, 'message'=> 'Hubo un error al obtener los grupos']);
    exit;
}

echo json_encode(['ok' => true, 'groups' => $groups]);

?>