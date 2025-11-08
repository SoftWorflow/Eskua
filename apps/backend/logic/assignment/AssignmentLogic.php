<?php

require_once(__DIR__ . "/../../middleware/auth.php");

require_once('IAssignmentLogic.php');
require_once(__DIR__ . "/../../DTO/GroupAssignment.php");
require_once(__DIR__ . "/../../DTO/File.php");
require_once(__DIR__ . "/../../persistence/assignment/AssignmentPersistenceFacade.php");

class AssignmentLogic implements IAssignmentLogic {

    public function createAssignmentWithFile(GroupAssignment $assignment, File $file): bool {
        AuthMiddleware::authorize(['teacher']);
        $teacherId = AuthMiddleware::authenticate()['user_id'];
        
        if ($assignment === null || $file === null || $teacherId === null)  return false;
        
        $assignmentPersistence = AssignmentPersistenceFacade::getInstance()->getIAssignmentPersistence();

        return $assignmentPersistence->createAssignment($assignment, $file, $teacherId);
    }

    public function createAssignment(GroupAssignment $assignment) : bool {
        AuthMiddleware::authorize(['teacher']);
        $teacherId = AuthMiddleware::authenticate()['user_id'];

        if ($assignment === null || $teacherId === null) return false;

        $userPersistence = UserPersistenceFacade::getInstance()->getIUserPersistence();

        return $userPersistence->createAssignment($assignment, $teacherId);
    }

    public function getAllAssignmentsCountAdmin() : int {
        AuthMiddleware::authorize(['admin']);

        $assignmentPersistence = AssignmentPersistenceFacade::getInstance()->getIAssignmentPersistence();

        $assignmentsCount = $assignmentPersistence->getAllAssignmentsCountAdmin();
    
        return $assignmentsCount;
    }

    public function getAllTurnedInAssignmentsCountAdmin() : int {
        AuthMiddleware::authorize(['admin']);

        $assignmentPersistence = AssignmentPersistenceFacade::getInstance()->getIAssignmentPersistence();

        $turnedInAssignmentsCount = $assignmentPersistence->getAllTurnedInAssignmentsCountAdmin();
    
        return $turnedInAssignmentsCount;
    }

    private static function needAuthentication(): bool {
        $user = AuthMiddleware::authenticate();

        return $user !== null;
    }

}

?>