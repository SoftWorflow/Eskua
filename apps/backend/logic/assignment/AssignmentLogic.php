<?php

require_once('IAssignmentLogic.php');
require_once(__DIR__ . "/../../DTO/GroupAssignment.php");
require_once(__DIR__ . "/../../DTO/File.php");
require_once(__DIR__ . "/../../persistence/assignment/AssignmentPersistenceFacade.php");

class AssignmentLogic implements IAssignmentLogic {

    public function createAssignment(GroupAssignment $assignment, File $file, int $teacherId): bool {
        if ($assignment === null || $file === null || $teacherId === null)  return false;
        
        $assignmentPersistence = AssignmentPersistenceFacade::getInstance()->getIAssignmentPersistence();

        return $assignmentPersistence->createAssignment($assignment, $file, $teacherId);
    }

}

?>