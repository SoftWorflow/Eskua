<?php

require_once(__DIR__ . "/../../../backend/logic/user/UserLogicFacade.php");
header('Content-Type: application/json');

$userLogic = UserLogicFacade::getInstance()->getIUserLogic();

$response = $userLogic->logout();

echo json_encode($response);

?>