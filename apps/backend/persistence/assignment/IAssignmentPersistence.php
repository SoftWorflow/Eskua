<?php

interface IAssignmentPersistence {
    public function createAssignmentWithFile(GroupAssignment $assignment, File $file, int $teacherId): bool;
    public function createAssignment(GroupAssignment $assignment, int $teacherId): bool;
    public function getAllAssignmentsCountAdmin() : int;
    public function getAllTurnedInAssignmentsCountAdmin() : int;
}

?>