<?php

interface IAssignmentLogic {
    public function createAssignmentWithFile(GroupAssignment $assignment, File $file): bool;
    public function createAssignment(GroupAssignment $assignment) : bool;
    public function getAllAssignmentsCountAdmin() : int;
    public function getAllTurnedInAssignmentsCountAdmin() : int;
}

?>