<?php

require_once('IGroupPersistence.php');
require_once(__DIR__ . '/../../DTO/Group.php');
require_once(__DIR__ . '/../../DTO/Users/User.php');
require_once(__DIR__ . '/../../db_connect.php');

class GroupPersistence implements IGroupPersistence {
    
    private $conn;
    private bool $res;

    public function __construct() {
        try {
            $dbConnection = new db_connect();
            $this->conn = $dbConnection->connect();
        } catch (Exception $e) {
            echo "GroupPersistence error: " . $e->getMessage();
        }
    }

    public function createGroup(Group $group) : bool {
        if ($group === null) return false;
        return true;
    }

    public function getGroupByCode($code) : ?array {
        if (empty($code)) return null;

        $sql = "select * from `groups` where `code` = ?;";

        try {

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$code]);
            $dbGroup = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            if ($dbGroup) {
                $name = $dbGroup['name'];
                $code = $dbGroup['code'];
                $groupId = $dbGroup['id'];
                
                $userLogic = UserLogicFacade::getInstance()->getIUserLogic();
                $dbTeacherA = $userLogic->getUserById($dbGroup['teacher']);
                $dbTeacher = $dbTeacherA[1];

                if ($dbTeacher) {
                    $username = $dbTeacher->getUsername();
                    $email = $dbTeacher->getEmail();
                    $display_name = $dbTeacher->getDisplayName();
                    $profile_picture = $dbTeacher->getProfilePictureUrl();
                    $password = $dbTeacher->getPassword();
                    $userRole = $dbTeacher->getUserRole();

                    $teacher = new User($username, $email, $display_name, $profile_picture, $password, $userRole);

                    $group = new Group($name, $code, $teacher);
                
                    return [$groupId, $group];
                }

                return null;
            }

            return null;

        } catch (PDOException $e) {
            echo "Error when getting group by code: " . $e->getMessage();
        }

        return null;
    }

    public function getGroup(int $groupId) : array {
        if ($groupId === null || empty($groupId)) return []; 

        $sql = "call getGroup(?);";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$groupId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result;
        } catch (PDOException $e) {
            error_log("Error getting the group: ". $e->getMessage());
        }

        return [];
    }
    
    public function getGroupMembers(int $groupId) : array {
        if ($groupId === null) return [];

        $sql = "call getGroupMembers(?);";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$groupId]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        } catch (PDOException $e) {
            error_log("Error getting group members: ". $e->getMessage());
        }

        return [];
    }

    public function deactivateAssignment(int $assignmentId) : ?bool {
        if ($assignmentId === null) return null;

        $sql = "call lDeactivateAssignment(?);";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$assignmentId]);
            $stmt->closeCursor();

            return true;
        } catch (PDOException $e) {
            echo "Error when deactivating an assignment: " . $e->getMessage();
        }

        return false;
    }

    public function getAllGroupsCountAdmin(): int {

        $sql = "select count(*) as total from `groups`;";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $groupsCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            $stmt->closeCursor();

            return $groupsCount;
        } catch (PDOException $e) {
            echo "Error when getting all groups for admin: " . $e->getMessage();
        }

        return 0;

    }

    public function getAllGroupsAdmin(): array {
        $sql = "select g.id, u.display_name as teacher, g.name as level from `groups` as g join `users` as u on g.teacher = u.id;";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $groupsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $groupsData;
        } catch (PDOException $e) {
            echo "Error when getting all groups for admin: " . $e->getMessage();
        }

        return [];
    }

    public function getSpecificGroupData(int $groupId) : array {
        $groupSql = "select g.id as id, u.display_name as teacher, g.name as level, g.code as code from `groups` as g join `users` as u on g.teacher = u.id where g.id = ?;";
        $membersSql = "select count(*) as total_members from `students` where `group` = ?;";
        $assignmentsSql = "select count(*) as total_assignments from `assigned_assignments` where `group` = ?;";

        try {
            $stmt = $this->conn->prepare($groupSql);
            $stmt->execute([$groupId]);
            $groupData = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            $stmt = $this->conn->prepare($membersSql);
            $stmt->execute([$groupId]);
            $groupData['members'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_members'];
            $stmt->closeCursor();

            $stmt = $this->conn->prepare($assignmentsSql);
            $stmt->execute([$groupId]);
            $groupData['assignments'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_assignments'];
            $stmt->closeCursor();

            return $groupData;
        } catch (PDOException $e) {
            echo "Error when getting specific group data for admin: " . $e->getMessage();
        } 

        return [];

    }

    public function searchGroupsByTeacherNameAdmin(string $teacherName) : array {
        $sql = "select g.id, u.display_name as teacher, g.name as level from `groups` as g join `users` as u on g.teacher = u.id where u.display_name like ?;";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(["%" . $teacherName . "%"]);
            $groupsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $groupsData;
        } catch (PDOException $e) {
            echo "Error when searching groups by teacher name for admin: " . $e->getMessage();
        }

        return [];
    }
    
}

?>