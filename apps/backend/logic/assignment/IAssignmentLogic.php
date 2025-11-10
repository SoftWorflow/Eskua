<?php

interface IAssignmentLogic {
    public function createAssignment(GroupAssignment $assignment, ?array $file = null): array;
    public function getAllAssignmentsCountAdmin() : int;
    public function getAllTurnedInAssignmentsCountAdmin() : int;
    public function modifyAssignment(int $assignmentId, GroupAssignment $assignment,bool $hasChangedFile, ?array $file = null, ?string $filePath = ''): array;
    public function createAssignmentFile(string $assignmentId, string $storageName, string $originalName,  string $mime, string $extention, int $size, int $userId): bool;
    public function getSpecificAssignment(int $assignmentId): array;
    public function turnInAssignment(int $assignmentId, string $text, ?array $fileData = null) : array;
    public function getTurnedInAssignmentsFromAssignment(int $assignmentId): array;
    public function getSpecificStudenAnswerById(int $studentAnswerId) : array;
}

?>