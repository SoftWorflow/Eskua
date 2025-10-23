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

$errorResponse = null;

if (empty($username)) {
    http_response_code(400);
    $errorResponse['ok'] = false;
    $errorResponse['username'] = ['error' => 'Este campo es olbigatorio'];
}

if (empty($displayName)) {
    http_response_code(400);
    $errorResponse['ok'] = false;
    $errorResponse['displayName'] = ['error' => 'Este campo es olbigatorio'];
}

if (empty($email)) {
    http_response_code(400);
    $errorResponse['ok'] = false;
    $errorResponse['email'] = ['error' => 'Este campo es obligatorio'];
}

if (empty($password)) {
    http_response_code(400);
    $errorResponse['ok'] = false;
    $errorResponse['password'] = ['error' => 'Este campo es olbigatorio'];
}

if (empty($confirmPassword)) {
    http_response_code(400);
    $errorResponse['ok'] = false;
    $errorResponse['confirmPassword'] = ['error' => 'Este campo es obligatorio'];
}

if (empty($userRole)) {
    http_response_code(400);
    $errorResponse['ok'] = false;
    $errorResponse['userRole'] = ['error' => 'Hay un error con el tipo de usuario'];
}

if (empty($groupCode)) {
    if ($userRole === "student") {
        http_response_code(400);
        $errorResponse['ok'] = false;
        $errorResponse['groupCode'] = ['error' => 'Tienes que ingresar el codigo del grupo'];
    }
} else {
    $groupLogic = GroupLogicFacade::getInstance()->getIGroupLogic();
    $group = $groupLogic->getGroupByCode($groupCode);
    if ($group === null) {
        http_response_code(400);
        $errorResponse['ok'] = false;
        $errorResponse['groupCode'] = ['error' => 'El codigo ingresado no es válido'];
    }
}

if (!UserRole::isValid($userRole)) {
    http_response_code(400);
    $errorResponse['ok'] = false;
    $errorResponse['userType'] = ['error' => 'El tipo de usuario no es válido'];
}

$userLogic = UserLogicFacade::getInstance()->getIUserLogic();

$dbUser = $userLogic->getUserByUsername($username);

if ($dbUser !== null) {
    http_response_code(409);
    $errorResponse['ok'] = false;
    $errorResponse['username'] = ['error' => 'El nombre usuario ya está en uso'];
}

$dbUser = $userLogic->getUserByEmail($email);

if ($dbUser !== null) {
    http_response_code(409);
    $errorResponse['ok'] = false;
    $errorResponse['email'] = ['error' => 'El email ya está en uso'];
}

if ($password !== $confirmPassword) {
    http_response_code(409);
    $errorResponse['ok'] = false;
    $errorResponse['confirmPassword'] = ['error' => 'Las contraseñas no coinciden'];   
}

if ($errorResponse !== null) {
    echo json_encode($errorResponse);
    exit;
}

$defaultUserProfilePicture = "192.168.1.44:8080/images/DefaultUserProfilePicture.jpg";

if ($userRole !== "student") {
    $user = new User($username, $email, $username, $defaultUserProfilePicture, $password, $userRole);

    if (!$userLogic->createUser($user)) {
        http_response_code(500);
        echo json_encode(['error' => 'There was an error creating the user']);
        exit;
    }

    http_response_code(201);
    echo json_encode(['ok' => true, 'message' => 'Student created successfully']);
    exit;
} else {
    $groupLogic = GroupLogicFacade::getInstance()->getIGroupLogic();
    $studentGroup = $groupLogic->getGroupByCode($groupCode);

    $student = new User($username, $email, $displayName, $defaultUserProfilePicture, $password, $userRole);

    if (!$userLogic->createStudent($student, $studentGroup[0])) {
        http_response_code(500);
        echo json_encode(['error' => 'There was an error creating the user']);
        exit;
    }

    http_response_code(201);
    echo json_encode(['ok' => true, 'message' => 'Student created successfully']);
    exit;
}
?>