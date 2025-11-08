<?php

require_once('IUserPersistence.php');
require_once(__DIR__ . '/../../DTO/Users/User.php');
require_once(__DIR__ . '/../../db_connect.php');

class UserPersistence implements IUserPersistence {

    private $conn;
    private bool $res;

    public function __construct() {
        try {
            $dbConnection = new db_connect();
            $this->conn = $dbConnection->connect();
        } catch (Exception $e) {
            echo "UsersPersistence error: " . $e->getMessage();
        }
    }

    public function createUser(User $user) : bool {
        if ($this->conn == null || $user == null) return false;

        $sql = "call createUser(?, ?, ?, ?, ?, ?);";

        $username = $user->getUsername();
        $email = $user->getEmail();
        $display_name = $user->getDisplayName();
        $profile_picture_url = $user->getProfilePictureUrl();
        $password = $user->getPassword();
        $role = $user->getUserRole();

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$username, $email, $display_name, $profile_picture_url, $password, $role]);
            $stmt->closeCursor();
            $res = true;
        } catch (PDOException $e) {
            print "Error while trying to create a user: " . $e->getMessage();
            $res = false;
        }

        return $res;
    }

    public function addGroupToStudent($userId, $groupId) : bool {
        if (empty($groupId)) return false;

        $sql = "update students set `group` = ? where `user` = ?;";

        try {

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$groupId, $userId]);
            $rowCount = $stmt->rowCount();
            $stmt->closeCursor();

            if ($rowCount === 0) {
                error_log("No student found with user ID: " . $userId);
                return false;
            }

            return true;

        } catch (PDOException $e) {
            print "Error while trying to assign a group to a student: " . $e->getMessage();
            return false;
        }
    }

    public function deleteUser(int $userId) : bool {
        
        $sql = "call fDeleteUser(?);";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$userId]);
            $affectedRows = $stmt->rowCount();
            $stmt->closeCursor();
            
            return $affectedRows > 0;
        } catch (PDOException $e) {
            print "Error while trying to eliminate a admin: " . $e->getMessage();
        }

        return false;
    }

    public function modifyUser(int $id, User $user) : bool {
        if ($this->conn == null || $id == null) return false;

        $sql = "call modifyUser(?, ?, ?, ?, ?, ?);";

        $newUsername = $user->getUsername();
        $newEmail = $user->getEmail();
        $newDisplayName = $user->getDisplayName();
        $newProfilePictureURL = $user->getProfilePictureUrl();
        $newPassword = $user->getPassword();

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id, $newUsername, $newEmail, $newDisplayName, $newProfilePictureURL, $newPassword]);
            $stmt->closeCursor();
            $res = true;
        } catch (PDOException $e) {
            print "Error while trying to modify a admin: " . $e->getMessage();
            $res = false;
        }
    
        return $res;
    }

    public function getUserById(int $id) : ?array {
        if ($this->conn == null || $id == null) return null;

        $sql = "call getUserById(?);";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id]);
            $dbUser = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            if ($dbUser) {
                $username = $dbUser['username'];
                $email = $dbUser['email'];
                $display_name = $dbUser['display_name'];
                $profile_picture_url = $dbUser['profile_picture_url'];
                $password = $dbUser['password'];
                $userRole = $dbUser['role'];
                
                $id = $dbUser['id'];

                $user = new User($username, $email, $display_name, $profile_picture_url, $password, $userRole);
                
                return [$id, $user];
            }

            return null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getUserByUsername(string $username) : ?array {
        if ($this->conn == null || empty($username)) return null;

        $sql = "call getUserByUsername(?);";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$username]);
            $dbUser = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            if ($dbUser) {
                $username = $dbUser['username'];
                $email = $dbUser['email'];
                $display_name = $dbUser['display_name'];
                $profile_picture_url = $dbUser['profile_picture_url'];
                $password = $dbUser['password'];
                $userRole = $dbUser['role'];
                
                $id = $dbUser['id'];

                $user = new User($username, $email, $display_name, $profile_picture_url, $password, $userRole);
                
                return [$id, $user];
            }

            return null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getUserByEmail(string $email) : ?array {
        if ($this->conn == null || empty($email)) return null;

        $sql = "call getUserByEmail(?);";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$email]);
            $dbUser = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            if ($dbUser) {
                $username = $dbUser['username'];
                $email = $dbUser['email'];
                $display_name = $dbUser['display_name'];
                $profile_picture_url = $dbUser['profile_picture_url'];
                $password = $dbUser['password'];
                $userRole = $dbUser['role'];
                
                $id = $dbUser['id'];

                $user = new User($username, $email, $display_name, $profile_picture_url, $password, $userRole);
                
                return [$id, $user];
            }

            return null;
        } catch (PDOException $e) {
            return null;
        }
    }

    // TOKENS
    public function createRefreshToken(int $userId, string $refreshToken, string $refreshExpire) : bool {
        if ($this->conn === null || empty($userId) || empty($refreshToken) || empty($refreshExpire)) return false;
        
        // Hacer procedimiento almacendado
        $sql = "call createRefreshToken(?, ?, ?);";
    
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$userId, $refreshToken, $refreshExpire]);
            $stmt->closeCursor();
            return true;
        } catch (PDOException $e) {
            error_log("Error storing refresh token: " . $e->getMessage());
            return false;
        }
    }

    public function getRefreshTokenByToken(string $refreshToken) : ?array {
        if (empty($refreshToken)) return null;
        
        $sql = "call getRefreshTokenByToken(?);";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$refreshToken]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Does not exist or it was revoked
            if (!$result) return null;

            return [
                'user_id' => (int)$result['user_id'],
                'refresh_token' => $result['refresh_token'],
                'expires_at' => $result['expires_at']
            ];

        } catch (PDOException $e) {
            error_log("Error getting refresh token: " . $e->getMessage());
            return null;
        }
    }

    public function revokeRefreshToken($refreshToken) : bool {
        if (empty($refreshToken)) return false;

        $sql = "revokeTokenByToken(?)";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$refreshToken]);
            return true;
        } catch (PDOException $e) {
            error_log("Error revoking token: " . $e->getMessage());
            return false;
        }
    }

    public function getStudentGroup($userId) : ?array {
        if ($userId === null) return null;

        $sql = "call getStudentGroup(?);";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) return null;

            return $result;
        } catch (PDOException $e) {
            error_log("Error getting student group: ". $e->getMessage());
        }

        return null;
    }

    public function getGroupMembers(int $groupId) : ?array {
        if ($groupId === null) return null;

        $sql = "call getGroupMembers(?);";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$groupId]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$result) return null;

            return $result;
        } catch (PDOException $e) {
            error_log("Error getting group members: ". $e->getMessage());
        }

        return null;
    }

    public function getAssignmentsFromGroup(int $groupId) : ?array {
        if ($groupId == null) return null;

        $sql = "call getAssignmentsFromGroup(?);";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$groupId]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$result) return null;

            return $result;
        } catch (PDOException  $e) {
            error_log("Error getting assignments from group: " . $e->getMessage());
        }

        return null;
    }

    public function getTeacherGroups(int $userId) : ?array {
        if ($userId === null) return null;

        $sql = "call getGroupsOfTeacher(?);";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$userId]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$result) return null;

            return $result;
        } catch (PDOException $e) {
            error_log("Error getting teacher groups: ". $e->getMessage());
        }

        return null;
    }

    public function getGroup(int $groupId) : ?array {
        if ($groupId == null) return null;

        $sql = "call getGroup(?);";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$groupId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) return null;

            return $result;
        } catch (PDOException $e) {
            error_log("Error getting the group: ". $e->getMessage());
        }

        return null;
    }

    public function createAssignment(GroupAssignment $assignment, int $teacherId) : bool {
        if ($assignment === null) return false;

        $sql = "call createAssignment(?, ?, ?, ?, ?, ?);";

        $groupId = $assignment->getGroupId();
        $name = $assignment->getName();
        $description = $assignment->getDescription();
        $maxScore = $assignment->getMaxScore();
        $dueDate = $assignment->getDueDate()->format('Y-m-d H:i:s');

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$teacherId, $groupId, $name, $description, $maxScore, $dueDate]);
            $stmt->closeCursor();

            return true;
        } catch (PDOException $e) {
            error_log("Error creating an assignment: ". $e->getMessage());
        }

        return false;
    }

    public function getAllUsersAdmin(): array {

        $sql = "select id, username, role from `users`;";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $users;
        } catch (PDOException $e) {
            print "Error when trying to get all users: " . $e->getMessage();
        }

        return [];

    }

    public function getSpecificUserData(int $userId) : array {

        $sql = "select profile_picture_url as profile_pic, id, username, display_name, email, role from `users` where id = ?;";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $user;
        } catch (PDOException $e) {
            print "Error when getting specific user data: " . $e->getMessage();
        }

        return [];

    }

    public function searchUsers(string $username) : array {

        $sql = "select id, username, role from `users` where username like ?;";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(["%" . $username . "%"]);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $users;
        } catch (PDOException $e) {
            print "Error when searching users: " . $e->getMessage();
        }

        return [];
        
    }

    public function getAllUsersCountAdmin() : array {

        $allUsersSql = "select count(*) as total from `users`;";
        $guestsSql = "select count(*) as total from `guests`;";
        $studentsSql = "select count(*) as total from `students`";
        $teacherSql = "select count(*) as total from `teachers`;";
        $adminSql = "select count(*) as total from `admins`;";

        try {
            $stmt = $this->conn->prepare($allUsersSql);
            $stmt->execute();
            $totalUserCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            $stmt->closeCursor();

            $stmt = $this->conn->prepare($guestsSql);
            $stmt->execute();
            $guestCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            $stmt->closeCursor();

            $stmt = $this->conn->prepare($studentsSql);
            $stmt->execute();
            $studentCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            $stmt->closeCursor();

            $stmt = $this->conn->prepare($teacherSql);
            $stmt->execute();
            $teacherCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            $stmt->closeCursor();

            $stmt = $this->conn->prepare($adminSql);
            $stmt->execute();
            $adminCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            return [
                'totalUsers' => $totalUserCount,
                'totalGuests' => $guestCount,
                'totalStudents' => $studentCount,
                'totalTeachers' => $teacherCount,
                'totalAdmins' => $adminCount
            ];
        } catch (PDOException $e) {
            print "Error getting all users count: " . $e->getMessage();
        }

        return [];

    }

}

?>