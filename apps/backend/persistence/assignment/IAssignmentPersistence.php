<?php

interface IAssignmentPersistence {
    public function createAssignment(GroupAssignment $assignment, File $file, int $teacherId): bool;
    public function getAllAssignmentsCountAdmin() : int;
    public function getAllTurnedInAssignmentsCountAdmin() : int;
}

?>