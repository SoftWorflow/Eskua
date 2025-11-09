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

}

?>