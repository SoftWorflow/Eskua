<?php
require_once(__DIR__ . "/../../../backend/DTO/Users/User.php");
require_once(__DIR__ . "/../../../backend/logic/user/UserLogicFacade.php");
require_once(__DIR__ . "/../../../backend/DTO/Users/UserRole.php");
require_once(__DIR__ . "/../../../backend/DTO/Group.php");
require_once(__DIR__ . "/../../../backend/logic/group/GroupLogicFacade.php");
require_once('token.php');

require(__DIR__ . '/../../../backend/vendor/autoload.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$username = $input['username'] ?? '';
$displayName = $input['displayName'] ?? '';
$email = $input['email'] ?? '';
$password = $input['password'] ?? '';
$confirmPassword = $input['confirmPassword'] ?? '';
$userRole = $input['userType'] ?? '';
$groupCode = $input['groupCode'] ?? '';

$userLogic = UserLogicFacade::getInstance()->getIUserLogic();

$response = $userLogic->register($username, $email, $displayName, $password, $confirmPassword, $userRole, $groupCode);

echo json_encode($response);

?>