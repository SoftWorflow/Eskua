<?php

require_once(__DIR__ . "/../../../backend/logic/user/UserLogicFacade.php");
header('Content-Type: application/json');

$userLogic = UserLogicFacade::getInstance()->getIUserLogic();

$usersData = $userLogic->getAllUsersAdmin();

echo json_encode($usersData);

?>