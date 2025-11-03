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
            return null;
        }
    }
    
}

?>