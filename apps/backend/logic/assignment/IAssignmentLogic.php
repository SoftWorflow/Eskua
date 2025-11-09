<?php

interface IAssignmentLogic {
    public function createAssignment(GroupAssignment $assignment, ?array $file = null): array;
    public function getAllAssignmentsCountAdmin() : int;
    public function getAllTurnedInAssignmentsCountAdmin() : int;
}

?>