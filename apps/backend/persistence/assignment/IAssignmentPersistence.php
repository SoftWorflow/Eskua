<?php

interface IAssignmentPersistence {
    public function createAssignmentWithFile(GroupAssignment $assignment, File $file, int $teacherId): bool;
    public function createAssignment(GroupAssignment $assignment, int $teacherId): bool;
    public function getAllAssignmentsCountAdmin() : int;
    public function getAllTurnedInAssignmentsCountAdmin() : int;
    public function modifyAssignment(GroupAssignment $assignment, int $assignmentId): bool;
    public function getAssignmentFileId(int $assignmentId): ?int;
    public function deleteAssignmentFileAssociation(int $fileId) : bool;
    public function createAssignmentFile(int $assignmentId, string $storageName, string $originalName, string $mime, string $extention, int $size, int $userId): bool;
    public function getSpecificAssignment(int $assignmentId): array;
    public function turnInAssignment(int $assignmentId, int $studentId, string $text) : bool;
    public function turnInAssignmentWithFile(int $assignmentId, int $studentId, string $text, string $storageName, string $origName, string $mime, string $extention, int $size) : bool;
}

?>