<?php

require_once("IFilePersistence.php");
require_once(__DIR__ . "/../../DTO/File.php");
require_once(__DIR__ . "/../../db_connect.php");

class FilePersistence implements IFilePersistence {

    private $conn;

    public function __construct() {
        try {
            $dbConnection = new db_connect();
            $this->conn = $dbConnection->connect();
        } catch (Exception $e) {
            echo "FilePersistence error: " . $e->getMessage();
        }
    }

    public function uploadFile(File $file, int $uploaderId): bool {
        if (!isset($file)) return false;

        $sql = "call createFile(?, ?, ?, ?, ?, ?);";

        $storageName = $file->getStorageName();
        $originalName = $file->getOriginalName();
        $mime = $file->getMime();
        $extension = $file->getExtension();
        $size = $file->getSize();

        try {
            $stmt = $this->conn->prepare(($sql));
            $stmt->execute([$storageName, $originalName, $mime, $extension, $size, $uploaderId]);
            $stmt->closeCursor();
            return true;
        } catch (PDOException $e) {
            print "Error while trying to upload a file: " . $e->getMessage();
            return false;
        }

    }
}

?>