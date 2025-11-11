<?php

require_once(__DIR__ . "/../../middleware/auth.php");

require_once("IUserLogic.php");
require_once(__DIR__ . "/../../DTO/GroupAssignment.php");
require_once(__DIR__ . "/../../DTO/Users/User.php");
require_once(__DIR__ . "/../../persistence/user/UserPersistenceFacade.php");
require_once(__DIR__ . "/../../persistence/group/GroupPersistenceFacade.php");
require_once(__DIR__ . "/../../logic/group/GroupLogicFacade.php");
require(__DIR__ . '/../../vendor/autoload.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class UserLogic implements IUserLogic {

    private const ACCESS_TOKEN_EXPIRE_TIME_IN_MINUTES = 15;
    private const DEFAULT_USER_PROFILE_PICTURE_URL = "/images/DefaultUserProfilePicture.webp";

    // CREATE USER
    public function createUser(User $user) : bool {
        $res = false;
        
        if ($user == null) return $res;

        $userPersistence = UserPersistenceFacade::getInstance()->getIUserPersistence();
        $dbUser = $userPersistence->getUserByUsername($user->getUsername());

        if ($dbUser != null) {
            $res = false;
            return $res;
        }
        
        $userPassword = $user->getPassword();

        $options = [
            'memory_cost' => 131072, // 128 MB
            'time_cost' => 4,
            'threads' => 2,
        ];

        $hashedPassword = password_hash($userPassword, PASSWORD_ARGON2ID, $options);

        $user->setPassword($hashedPassword);

        $res = $userPersistence->createUser($user);
        return $res;
    }

    public function createStudent(User $user, $groupId) : bool {
        if ($user === null || empty($groupId)) return false;
        
        if (!$this->createUser($user)) {
            error_log("Failed to create user: " . $user->getUsername());
            return false;
        }
        
        $userPersistence = UserPersistenceFacade::getInstance()->getIUserPersistence();
        $dbUser = $userPersistence->getUserByUsername($user->getUsername());

        if (!$dbUser) {
            error_log("User was created but could not be retrieved: " . $user->getUsername());
            return false;
        }

        $userId = $dbUser[0];

        $result = $userPersistence->addGroupToStudent($userId, $groupId);
        
        if (!$result) {
            error_log("Failed to assign group $groupId to student $userId");
        }
        
        return $result;
    }

    public function deleteUserById(int $userId) : array {
        if (empty($userId) || $userId === null) {
            return ['ok' => false, 'error' => 'No se recibio el identificador del usuario'];
        }

        $userPersistence = UserPersistenceFacade::getInstance()->getIUserPersistence();

        if ($userPersistence->deleteUser($userId)) {
            return ['ok' => true, 'message' => 'Usuario borrado con exito!'];
        } else {
            return ['ok' => false, 'message' => 'Hubo un error al eliminar el usuario!'];
        }
    }

    public function deleteUserByUsername(string $username) : bool {
        $res = false;

        if (empty($username)) return $res;
        
        $userPersistence = UserPersistenceFacade::getInstance()->getIUserPersistence();
        
        $result = $userPersistence->getUserByUsername($username);

        if ($result === null) {
            return false;
        }

        $id = $result[0];

        $res = $userPersistence->deleteUser($id);

        return $res;
    }

    /*
     *  MODIFY USER
    */
    public function modifyUser(int $id, User $user) : bool {
        $res = false;

        if ($user === null) return $res;

        $userPersistence = UserPersistenceFacade::getInstance()->getIUserPersistence();
        
        $res = $userPersistence->modifyUser($id, $user);

        return $res;
    }

    /*
     *  GET USER
    */
    public function getUserById(?int $userId = null) : ?array {

        if ($userId === null) {
            $userId = AuthMiddleware::authenticate()['user_id'];
            if ($userId === null) return null;
        }

        $userPersistence = UserPersistenceFacade::getInstance()->getIUserPersistence();
        $result = $userPersistence->getUserById($userId);

        if ($result === null) return null;

        $user = $result[1];
        return [$userId, $user];
    }

    public function getUserByUsername(string $username) : ?array {
        if (empty($username)) return ['ok' => false, 'error' => 'No se recibio el nombre de usuario'];

        $userPersistence = UserPersistenceFacade::getInstance()->getIUserPersistence();
        $result = $userPersistence->getUserByUsername($username);

        if (empty($result)) {
            return ['ok' => false, 'error' => 'Usuario no encontrado'];
        }

        return ['ok' => true, 'user' => $result];
    }

    public function register(string $username, string $email, string $displayName, string $password, string $confirmPassword, string $userRole, ?string $groupCode = null) : array {

        $errorResponse = null;
        if (empty($username)) {
            http_response_code(400);
            $errorResponse['ok'] = false;
            $errorResponse['username'] = ['error' => 'Este campo es olbigatorio'];
        }

        if (strlen($username) > 30) {
            http_response_code(400);
            $errorResponse['ok'] = false;
            $errorResponse['username'] = ['error' => 'Este campo es muy largo'];
        }

        if (empty($displayName)) {
            http_response_code(400);
            $errorResponse['ok'] = false;
            $errorResponse['displayName'] = ['error' => 'Este campo es olbigatorio'];
        }

        if (strlen($displayName) > 30) {
            http_response_code(400);
            $errorResponse['ok'] = false;
            $errorResponse['displayName'] = ['error' => 'Este campo es muy largo'];
        }

        if (empty($email)) {
            http_response_code(400);
            $errorResponse['ok'] = false;
            $errorResponse['email'] = ['error' => 'Este campo es obligatorio'];
        } else {
            if (strlen($email) > 255) {
                http_response_code(400);
                $errorResponse['ok'] = false;
                $errorResponse['email'] = ['error' => 'Este campo es muy largo'];
            } else {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    http_response_code(400);
                    $errorResponse['ok'] = false;
                    $errorResponse['email'] = ['error' => 'El formato del correo no es válido'];
                }
            }
        }

        if (strlen($email) > 255) {
            http_response_code(400);
            $errorResponse['ok'] = false;
            $errorResponse['email'] = ['error' => 'Este campo es muy largo'];
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

        if (!empty($password) && !empty($confirmPassword) && $password !== $confirmPassword) {
            http_response_code(409);
            $errorResponse['ok'] = false;
            $errorResponse['confirmPassword'] = ['error' => 'Las contraseñas no coinciden'];
        }

        if (!empty($password)) {
            $low = preg_match('/[a-z]/', $password);
            $up  = preg_match('/[A-Z]/', $password);
            $dig = preg_match('/\d/', $password);
            $sym = preg_match('/\W/', $password);
            if (!$low || !$up || !$dig || !$sym) {
                http_response_code(400);
                $errorResponse['ok'] = false;
                $errorResponse['password'] = ['error' => 'La contraseña debe contener minúsculas, mayúsculas, números y símbolos'];
            }
        }

        $minPasswordLength = 8;
        if (!empty($password) && strlen($password) < $minPasswordLength) {
            http_response_code(400);
            $errorResponse['ok'] = false;
            $errorResponse['password'] = ['error' => "La contraseña debe tener al menos {$minPasswordLength} caracteres"];
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

        if (!UserRole::isValid($userRole) || $userRole === 'admin') {
            http_response_code(400);
            $errorResponse['ok'] = false;
            $errorResponse['userType'] = ['error' => 'No tienes permiso para crear un usuario con este rol'];
        }

        // Verify that the username is not taken
        $dbUser = $this->getUserByUsername($username)['ok'];

        if ($dbUser) {
            http_response_code(400);
            $errorResponse['ok'] = false;
            $errorResponse['username'] = ['error' => 'El nombre de usuario ya está en uso'];
        }

        // Verify that the email is not taken
        $dbUserByEmail = $this->getUserByEmail($email)['ok'];

        if ($dbUserByEmail) {
            http_response_code(409);
            $errorResponse['ok'] = false;
            $errorResponse['email'] = ['error' => 'El correo electrónico ya está en uso'];
        }

        if ($errorResponse !== null) {
            return $errorResponse;
        }

        $user = new User($username, $email, $displayName, UserLogic::DEFAULT_USER_PROFILE_PICTURE_URL, $password, $userRole);

        if ($userRole !== "student") {
            if (!$this->createUser($user)) {
                http_response_code(500);
                return ['ok' => false, 'error' => 'Hubo un error al crear el usuario'];
            }

            http_response_code(201);
            return ['ok' => true, 'message' => 'Usuario creado con exito'];
        } else {
            $groupLogic = GroupLogicFacade::getInstance()->getIGroupLogic();
            $studentGroup = $groupLogic->getGroupByCode($groupCode);

            if ($studentGroup === null) {
                http_response_code(400);
                return ['ok' => false, 'error' => 'El codigo de grupo no es válido'];
            }

            $groupId = $studentGroup[0];

            if (!$this->createStudent($user, $groupId)) {
                http_response_code(500);
                return ['ok' => false, 'error' => 'Hubo un error al crear el estudiante'];
            }

            http_response_code(201);
            return ['ok' => true, 'message' => 'Estudiante creado con exito'];
        }

    }

    public function login(string $username, string $password) : array {
        $user = $this->getUserByUsername($username);

        if (!$user['ok']) {
            return ['ok' => false, 'error' => 'Usuario o contraseña incorrectos'];
        }

        $userData = $user['user'][1];

        if (!password_verify($password, $userData->getPassword())) {
            return ['ok' => false, 'error' => 'Usuario o contraseña incorrectos'];
        }

        $token = $this->generateToken($userData);
        
        if ($token === null) {
            http_response_code(500);
            return ['ok' => false, 'error' => 'Error generando el token'];
        }
        
        $token['ok'] = true;
        return $token;
    }

    public function logout() : array {
        if (!isset($_COOKIE['refresh_token'])) {
            error_log("Logout: No refresh token cookie found");
            return ['ok' => true, 'message' => 'No active session'];
        }

        $refreshToken = $_COOKIE['refresh_token'];

        // Revoke token on the DB
        $revoked = $this->revokeRefreshToken($refreshToken);

        if (!$revoked) {
            error_log("Logout: Failed to revoke token in database");
        }

        $cookieDeleted = setcookie('refresh_token', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);

        if (!$cookieDeleted) {
            error_log("Logout: Failed to delete cookie");
        }

        error_log("Logout: Token revoked=" . ($revoked ? 'yes' : 'no') . ", Cookie deleted=" . ($cookieDeleted ? 'yes' : 'no'));
    
        return [
            'ok' => true, 
            'token_revoked' => $revoked,
            'cookie_deleted' => $cookieDeleted
        ];
    }

    public function getUserByEmail(string $email) : ?array {
        if (empty($email)) return null;

        $userPersistence = UserPersistenceFacade::getInstance()->getIUserPersistence();
        $result = $userPersistence->getUserByEmail($email);

        if (empty($result)) {
            return ['ok' => false, 'error' => 'Usuario no encontrado'];
        }

        return ['ok' => true, 'user' => $result];
    }

    // TOKENS
    public function generateToken(User $user) : ?array {
        if ($user === null) return ["error" => "Usuario no recibido"];
        
        $secretKey = getenv('CLIENT_TOKEN_SECRET');
        $issuedAt = time();
        $accessExpire = $issuedAt + (UserLogic::ACCESS_TOKEN_EXPIRE_TIME_IN_MINUTES * 60);

        $username = $user->getUsername();
        $dbUser = $this->getUserByUsername($username);

        if (!$dbUser) return null;
        $userId = $dbUser['user'][0];

        // Payload of the access token
        $payload = [
            'user_id' => $userId,
            'username' => $user->getUsername(),
            'role' => $user->getUserRole(),
            'iat' => $issuedAt,
            'exp' => $accessExpire
        ];

        $accessToken = JWT::encode($payload, $secretKey, 'HS256');

        // Random refresh token
        $refreshToken = bin2hex(random_bytes(32));
        $refreshExpireTimestamp = $issuedAt + (60*60*24*30);
        $refreshExpireDate = date('Y-m-d H:i:s', $refreshExpireTimestamp);

        $userPersistence = UserPersistenceFacade::getInstance()->getIUserPersistence();
        if (!$userPersistence->createRefreshToken($userId, $refreshToken, $refreshExpireDate)) return ["error" => "Error generando el refresh token"];


        setcookie(
            "refresh_token",
            $refreshToken,
            [
                'expires' => $refreshExpireTimestamp,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict'
            ]
        );

        // Sent to the frontend
        $result = [
            'access_token' => $accessToken,
            'access_expires_at' => date('Y-m-d H:i:s', $accessExpire)
        ];

        $userData = [
            'display_name' => $user->getDisplayName(),
            'profile_picture_url' => $user->getProfilePictureUrl()
        ];

        $result['user'] = $userData;

        return $result;
    }

    public function refreshToken() : ?array {
        if (!isset($_COOKIE['refresh_token'])) return null;

        $refreshToken = $_COOKIE['refresh_token'];
        $userPersistence = UserPersistenceFacade::getInstance()->getIUserPersistence();

        $dbRefresh = $userPersistence->getRefreshTokenByToken($refreshToken);
        // Invalid token
        if (!$dbRefresh) return null;

        $expiresAt = strtotime($dbRefresh['expires_at']);
        // The token has expired
        if ($expiresAt < time()) return null;

        $dbUser = $this->getUserById($dbRefresh['user_id']);
        if (!$dbUser) return null;
        $user = $dbUser[1];

        $secretKey = getenv('CLIENT_TOKEN_SECRET');
        $issuedAt = time();
        $accessExpire = $issuedAt + (UserLogic::ACCESS_TOKEN_EXPIRE_TIME_IN_MINUTES * 60);

        // Random refresh token
        $payload = [
            'user_id' => $dbRefresh['user_id'],
            'username' => $user->getUsername(),
            'role' => $user->getUserRole(),
            'iat' => $issuedAt,
            'exp' => $accessExpire
        ];

        $accessToken = JWT::encode($payload, $secretKey, 'HS256');

        $result = [
            'access_token' => $accessToken,
            'access_expires_at' => date('Y-m-d H:i:s', $accessExpire)
        ];

        $userData = [
            'display_name' => $user->getDisplayName(),
            'profile_picture_url' => $user->getProfilePictureUrl()
        ];

        $result['user'] = $userData;

        return $result;
    }

    public function revokeRefreshToken($refreshToken) : bool {
        if (empty($refreshToken)) return false;

        $userPersistence = UserPersistenceFacade::getInstance()->getIUserPersistence();

        return $userPersistence->revokeRefreshToken($refreshToken);
    }

    public function getStudentGroup() : ?array {
        AuthMiddleware::authorize(['student']);

        $userId = AuthMiddleware::authenticate()['user_id'];
        
        if ($userId == null) return null;

        $userPersistence = UserPersistenceFacade::getInstance()->getIUserPersistence();

        return $userPersistence->getStudentGroup($userId);
    }

    public function getSpecificUserData(int $userId) : array {
        AuthMiddleware::authorize(['admin']);

        if (empty($userId)) {
            return ['ok' => false, 'error' => 'Nose riecibio el identificador del usuario'];
        }

        $userPersistence = UserPersistenceFacade::getInstance()->getIUserPersistence();

        $user = $userPersistence->getSpecificUserData($userId);

        if (empty($user)) {
            return ['ok' => false, 'error' => 'Hubo un error al buscar al usuario'];
        }

        return ['ok' => true, $user];
    }

    public function getAssignmentsFromGroup(int $groupId) : array {
        if ($groupId === null) {
            return ['ok' => false, 'error' => 'No se recibio el identificador del grupo'];
        }

        $userId = AuthMiddleware::authenticate()['user_id'];
        if ($userId === null) {
            return ['ok' => false, 'error' => 'No se pudo autenticar el usuario'];
        }

        $userPersistence = UserPersistenceFacade::getInstance()->getIUserPersistence();

        $assignments = $userPersistence->getAssignmentsFromGroup($groupId, $userId);

        if (empty($assignments)) {
            return ['ok' => false, 'error' => 'No hay tareas'];
        }

        if (AuthMiddleware::authenticate()['role'] === 'student') {
            if ($assignments !== null) {
                $responseAssignments = [];
                $tz = new DateTimeZone(date_default_timezone_get());
                $currentDate = new DateTime('now', $tz);
                $i = 0;
                foreach ($assignments as $assignment) {
                    $dueDate = new DateTime($assignment['dueDate'], $tz);
                    if ($dueDate > $currentDate) {
                        $assignment['isOverdue'] = false;
                    } else {
                        $assignment['isOverdue'] = true;
                    }

                    if (!$assignment['isNotActive']) {
                        $responseAssignments[$i][] = $assignment;
                    }
                    $i = $i + 1;
                }
                $assignments = $responseAssignments;
            }
        }

        $assignments = array_values($assignments);

        return ['ok' => true, 'tasks' => $assignments];
    }

    public function getTeacherGroups(int $userId) : array {
        AuthMiddleware::authorize(['teacher']);
        
        if ($userId === null) {
            return ['ok' => false, 'error' => 'No se recibio el identificador del usuario'];
        }

        $userPersistence = UserPersistenceFacade::getInstance()->getIUserPersistence();

        $groups = $userPersistence->getTeacherGroups($userId);

        if (empty($groups)) {
            return ['ok' => false, 'error' => 'Hubo un error al obtener los grupos'];
        }

        return ['ok' => true, 'groups' => $groups];
    }

    public function getAllUsersAdmin(): array {
        AuthMiddleware::authorize(['admin']);

        $userPersistence = UserPersistenceFacade::getInstance()->getIUserPersistence();

        $users = $userPersistence->getAllUsersAdmin();

        if (empty($users)) {
            return ['ok' => false, 'error' => 'Hubo un error al encontrar los usuarios'];
        }

        return $users;
    }

    public function searchUsers(string $username) : array {
        AuthMiddleware::authorize(['admin']);

        $userPersistence = UserPersistenceFacade::getInstance()->getIUserPersistence();

        $users = $userPersistence->searchUsers($username);

        if (empty($users)) {
            return ['ok' => false, 'message' => 'No se encontraron resultados'];
        }

        return ['ok' => true, $users];
    }

    public function getAllUsersCountAdmin() : array {
        AuthMiddleware::authorize(['admin']);

        $userPersistence = UserPersistenceFacade::getInstance()->getIUserPersistence();

        $usersCount = $userPersistence->getAllUsersCountAdmin();

        if (empty($usersCount)) {
            return ['ok' => false, 'error' => 'Hubo un error al contar los usuarios'];
        }

        return $usersCount;
    }

}

?>