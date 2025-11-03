<?php

require_once("IMaterialPersistence.php");
require_once(__DIR__ . '/../../DTO/PublicMaterial.php');
require_once(__DIR__ . '/../../DTO/File.php');
require_once(__DIR__ . '/../../db_connect.php');

class MaterialPersistence implements IMaterialPersistence {

    private $conn;

    public function __construct() {
        try {
            $dbConnection = new db_connect();
            $this->conn = $dbConnection->connect();
        } catch (Exception $e) {
            echo "MaterialPersistence error: " . $e->getMessage();
        }
    }

    public function uploadMaterial(PublicMaterial $publicMaterial, File $file, int $uploaderId): bool {
        if ($publicMaterial == null || $file == null || $uploaderId == null) return false;
        
        $materialTitle = $publicMaterial->getTitle();
        $materialDescription = $publicMaterial->getDescription();

        $sql = "call createFullPublicMaterial(?, ?, ?, ?, ?, ?, ?, ?);";

        $materialTitle = $publicMaterial->getTitle();
        $materialDescription = $publicMaterial->getDescription();

        $storageName = $file->getStorageName();
        $originalName = $file->getOriginalName();
        $mime = $file->getMime();
        $extension = $file->getExtension();
        $size = $file->getSize();

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$materialTitle, $materialDescription, $storageName, $originalName, $mime, $extension, $size, $uploaderId]);
            return true;
        } catch (PDOException $e) {
            print "Error while trying to upload a material: " . $e->getMessage();
            return false;
        }

    }
}

?>