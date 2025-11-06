<?php

interface IAssignmentPersistence {
    public function createAssignment(GroupAssignment $assignment, File $file, int $teacherId): bool;
}

?>