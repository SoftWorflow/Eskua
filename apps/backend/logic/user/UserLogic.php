<?php

require_once("IUserLogic.php");
require_once(__DIR__ . "/../../DTO/Users/User.php");
require_once(__DIR__ . "/../../persistence/user/UserPersistenceFacade.php");
require(__DIR__ . '/../../vendor/autoload.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class UserLogic implements IUserLogic {

    private int $ACCESS_TOKEN_EXPIRE_TIME_IN_MINUTES = 15;

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

    /*
     *  DELETE USER
    */
    public function deleteUserById(int $id) : bool {
        $res = false;

        if ($id == null) return $res;
        if ($id <= 0) return $res;

        $userPersistence = UserPersistenceFacade::getInstance()->getIUserPersistence();
        $res = $userPersistence->deleteUser($id);
        return $res;
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
    public function getUserById(int $id) : ?array {
        if ($id === null) return null;

        $userPersistence = UserPersistenceFacade::getInstance()->getIUserPersistence();
        $result = $userPersistence->getUserById($id);

        $user = $result[1];
        return [$id, $user];
    }

    public function getUserByUsername(string $username) : ?array {
        if (empty($username)) return null;

        $userPersistence = UserPersistenceFacade::getInstance()->getIUserPersistence();
        $result = $userPersistence->getUserByUsername($username);

        if ($result === null) {
            return null;
        }

        $id = $result[0];
        $user = $result[1];

        return [$id, $user];
    }

    public function getUserByEmail(string $email) : ?array {
        if (empty($email)) return null;

        $userPersistence = UserPersistenceFacade::getInstance()->getIUserPersistence();
        $result = $userPersistence->getUserByEmail($email);

        if ($result === null || !is_array($result) || count($result) < 2) {
            return null;
        }

        $id = $result[0];
        $user = $result[1];

        return [$id, $user];
    }

    // TOKENS
    public function generateToken(User $user) : ?array {
        if ($user === null) return ["error" => "Usuario no recibido"];
        
        $secretKey = getenv('CLIENT_TOKEN_SECRET');
        $issuedAt = time();
        $accessExpire = $issuedAt + ($this->ACCESS_TOKEN_EXPIRE_TIME_IN_MINUTES * 60);

        $username = $user->getUsername();
        $dbUser = $this->getUserByUsername($username);

        if (!$dbUser) return null;
        $userId = $dbUser[0];

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
                'secure' => false,
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
        $accessExpire = $issuedAt + ($this->ACCESS_TOKEN_EXPIRE_TIME_IN_MINUTES * 60);

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

    public function getStudentGroup($userId) : ?array {
        if ($userId == null) return null;

        $userPersistence = UserPersistenceFacade::getInstance()->getIUserPersistence();

        return $userPersistence->getStudentGroup($userId);
    }

    public function getGroupMembers(int $groupId) : ?array {
        if ($groupId == null) return null;

        $userPersistence = UserPersistenceFacade::getInstance()->getIUserPersistence();

        return $userPersistence->getGroupMembers($groupId);
    }

    public function getAssignmentsFromGroup(int $groupId) : ?array {
        if ($groupId == null) return null;

        $userPersistence = UserPersistenceFacade::getInstance()->getIUserPersistence();

        return $userPersistence->getAssignmentsFromGroup($groupId);
    }

    public function getTeacherGroups(int $userId) : ?array {
        if ($userId == null) return null;

        $userPersistence = UserPersistenceFacade::getInstance()->getIUserPersistence();

        return $userPersistence->getTeacherGroups($userId);
    }

    public function getGroup(int $groupId) : ?array {
        if ($groupId == null) return null;

        $userPersistence = UserPersistenceFacade::getInstance()->getIUserPersistence();

        return $userPersistence->getGroup($groupId);
    }

}

?>