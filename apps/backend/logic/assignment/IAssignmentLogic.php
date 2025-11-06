<?php

interface IAssignmentLogic {
    public function createAssignment(GroupAssignment $assignment, File $file, int $teacherId): bool;
}

?>