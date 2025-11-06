<?php

require_once("IMaterialPersistence.php");
require_once(__DIR__ . '/../../DTO/PublicMaterial.php');
require_once(__DIR__ . '/../../DTO/File.php');
require_once(__DIR__ . '/../../db_connect.php');

class MaterialPersistence implements IMaterialPersistence
{

    private $conn;

    public function __construct()
    {
        try {
            $dbConnection = new db_connect();
            $this->conn = $dbConnection->connect();
        } catch (Exception $e) {
            echo "MaterialPersistence error: " . $e->getMessage();
        }
    }

    public function uploadMaterial(PublicMaterial $publicMaterial, File $file, int $uploaderId): bool
    {
        if ($publicMaterial == null || $file == null || $uploaderId == null)
            return false;

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

    public function getAllMaterials(): ?array
    {
        $sql = "call getAllPublicMaterials();";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        } catch (PDOException $e) {
            print "Error getting all the public materials: " . $e->getMessage();
        }

        return null;
    }

    public function getMaterial(int $materialId): ?array
    {
        if ($materialId === null)
            return null;

        $sql = "call getMaterialById(?);";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$materialId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            print "Error getting the material: " . $e->getMessage();
        }

        return null;
    }
}

?>