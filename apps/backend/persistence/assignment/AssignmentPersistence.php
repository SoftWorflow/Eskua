<?php

require_once("IAssignmentPersistence.php");
require_once(__DIR__ . '/../../DTO/GroupAssignment.php');
require_once(__DIR__ . '/../../DTO/File.php');
require_once(__DIR__ . '/../../db_connect.php');

class AssignmentPersistence implements IAssignmentPersistence {

    private $conn;

    public function __construct() {
        try {
            $dbConnection = new db_connect();
            $this->conn = $dbConnection->connect();
        } catch (Exception $e) {
            echo "MaterialPersistence error: " . $e->getMessage();
        }
    }

    public function createAssignmentWithFile(GroupAssignment $assignment, File $file, int $teacherId): bool {
        if ($assignment === null || $file === null || $teacherId === null) return false;

        $sql = "call createFullAssignment(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";

        // Assignment
        $groupId = $assignment->getGroupId();
        $assignmentName = $assignment->getName();
        $assignmentDescription = $assignment->getDescription();
        $assignmentMaxScore = $assignment->getMaxScore();
        $assignmentDueDate = $assignment->getDueDate()->format('Y-m-d H:i:s');

        // File
        $fileStorageName = $file->getStorageName();
        $fileOriginalName = $file->getOriginalName();
        $fileMime = $file->getMime();
        $fileExtension = $file->getExtension();
        $fileSize = $file->getSize();

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$teacherId, $groupId, $assignmentName, $assignmentDescription, $assignmentMaxScore, $assignmentDueDate, $fileStorageName, $fileOriginalName, $fileMime, $fileExtension, $fileSize]);
            $stmt->closeCursor();

            return true;
        } catch (PDOException $e) {
            print "Error while trying to upload a material: " . $e->getMessage();
        }

        return false;
    }

    public function createAssignment(GroupAssignment $assignment, int $teacherId): bool {
        if ($assignment === null || $teacherId === null) return false;

        $sql = "call createAssignment(?, ?, ?, ?, ?, ?);";

        // Assignment
        $groupId = $assignment->getGroupId();
        $assignmentName = $assignment->getName();
        $assignmentDescription = $assignment->getDescription();
        $assignmentMaxScore = $assignment->getMaxScore();
        $assignmentDueDate = $assignment->getDueDate()->format('Y-m-d H:i:s');

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$teacherId, $groupId, $assignmentName, $assignmentDescription, $assignmentMaxScore, $assignmentDueDate]);
            $stmt->closeCursor();

            return true;
        } catch (PDOException $e) {
            print "Error while trying to upload a material: " . $e->getMessage();
        }

        return false;
    }

    public function getAllAssignmentsCountAdmin() : int {

        $sql = "select count(*) as total from `assigned_assignments`;";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $assignmentsCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            $stmt->closeCursor();

            return $assignmentsCount;
        } catch (PDOException $e) {
            print "Error while trying to get assignments count: " . $e->getMessage();
        }

        return 0;

    }

    public function getAllTurnedInAssignmentsCountAdmin() : int {

        $sql = "select count(*) as total from `turned_in_assignments`;";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $turnedInAssignmentsCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            $stmt->closeCursor();

            return $turnedInAssignmentsCount;
        } catch (PDOException $e) {
            print "Error while trying to get turned in assignments count: " . $e->getMessage();
        }

        return 0;

    }

    public function modifyAssignment(GroupAssignment $assignment, int $assignmentId): bool {

        $sql = "call modifyAssignment(?, ?, ?, ?, ?);";

        $assignmentName = $assignment->getName();
        $assignmentDescription = $assignment->getDescription();
        $assignmentMaxScore = $assignment->getMaxScore();
        $assignmentDueDate = $assignment->getDueDate()->format('Y-m-d H:i:s');

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$assignmentId, $assignmentName, $assignmentDescription, $assignmentMaxScore, $assignmentDueDate]);
            $stmt->closeCursor();

            return true;
        } catch (PDOException $e) {
            print "Error while trying to modify assignment: " . $e->getMessage();
        }
        return false;

    }

    public function getAssignmentFileId(int $assignmentId): ?int {
        if (empty($assignmentId) || $assignmentId === null) return null;
        
        $sql = "select f.`id` AS id from `files` as f join `assigned_assignments_files` as aaf on f.`id` = aaf.`file` join `assigned_assignments` as aa on aaf.`assigned_assignment` = aa.`id` join `assignments` as a on aa.`assignment` = a.`id` where a.`id` = ?;";
    
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$assignmentId]);
            $fileId = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
            $stmt->closeCursor();

            return $fileId;
        } catch (PDOException $e) {
            print "Error while trying to get assignment file id: " . $e->getMessage();
        }

        return null;
    }

    public function deleteAssignmentFileAssociation(int $fileId) : bool {
        if (empty($fileId) || $fileId === null) return false;
        
        $sql = "delete from `files` as f where f.`id` = ?;";
    
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$fileId]);
            $affectedRows = $stmt->rowCount();
            $stmt->closeCursor();

            return $affectedRows > 0;
        } catch (PDOException $e) {
            print "Error while trying to delete assignment file association: " . $e->getMessage();
        }

        return false;
    }

    public function createAssignmentFile(int $assignmentId, string $storageName, string $originalName, string $mime, string $extention, int $size, int $userId): bool {
        if (empty($assignmentId) || empty($storageName) || empty($originalName) || empty($mime) || empty($extention) || empty($size) || empty($userId)) {
            return false;
        }

        $sql = "call createAssignmentFile(?, ?, ?, ?, ?, ?, ?);";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$assignmentId, $storageName, $originalName, $mime, $extention, $size, $userId]);
            $affectedRows = $stmt->rowCount();
            $stmt->closeCursor();

            return $affectedRows > 0;
        } catch (PDOException $e) {
            print "Error while trying to create assignment file: " . $e->getMessage();
        }

        return false;
    }

    public function getSpecificAssignment(int $assignmentId): array {
        if ($assignmentId === null || empty($assignmentId)) {
            return [];
        }
        
        $sql = "call getAssignmentById(?);";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$assignmentId]);
            $assignmentData = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $assignmentData ? $assignmentData : [];
        } catch (PDOException $e) {
            print "Error while trying to get specific assignment: " . $e->getMessage();
        }

        return [];
    }

    public function turnInAssignment(int $assignmentId, int $studentId, string $text) : bool {
        if ($assignmentId === null || empty($assignmentId)) {
            return false;
        }

        $sql = "call turnInAssignment(?, ?, ?);";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$assignmentId, $studentId, $text]);
            $stmt->closeCursor();

            return true;
        } catch (PDOException $e) {
            print "Error when turning in an assignment". $e->getMessage();
        }

        return false;
    }

    public function turnInAssignmentWithFile(int $assignmentId, int $studentId, string $text, string $storageName, string $origName, string $mime, string $extention, int $size) : bool {
        if ($assignmentId === null || $studentId === null || $studentId === null || empty($text) || empty($storageName) || empty($origName) || empty($mime) || empty($extention) || $size === null) {
            return false;
        }

        $sql = "call turnInAssignmentWithFile(?, ?, ?, ?, ?, ?, ?, ?);";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$assignmentId, $studentId, $text, $storageName, $origName, $mime, $extention, $size]);
            $stmt->closeCursor();

            return true;
        } catch (PDOException $e) {
            print "Error when trying to turn in an assignment with file: ". $e->getMessage();
        }

        return false;
    }

}

?>