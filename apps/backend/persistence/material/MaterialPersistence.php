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

    public function deleteMaterial(int $materialId): bool {
        
        $sql = "call fDeleteMaterial(?);";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$materialId]);
            $affectedRows = $stmt->rowCount();

            return $affectedRows > 0;
        } catch (PDOException $e) {
            print "Error when trying yo delete a material: " . $e->getMessage();
        }

        return false;

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

    public function getAllMaterialsAdmin(): array {

        $sql = "select pm.id as id, pm.title as title, f.extension as `type` from public_materials as pm join public_materials_files as pmf on pm.id = pmf.public_material join files as f on pmf.file = f.id;";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            print "Error getting all the public materials: " . $e->getMessage();
        }

        return [];

    }

    public function getSpecificMaterial(int $materialId): array {

        $sqlMaterial = "select pm.id, pm.title, pm.description, pm.uploaded_date as uploadedDate, f.extension as type from public_materials as pm join public_materials_files as pmf on pm.id = pmf.public_material join files as f on pmf.file = f.id where pm.id = ?;";
        $sqlFile = "select f.storage_name as storageName from public_materials as pm join public_materials_files as pmf on pm.id = pmf.public_material join files as f on pmf.file = f.id where pm.id = ?;";

        try {
            $stmt = $this->conn->prepare($sqlMaterial);
            $stmt->execute([$materialId]);
            $materialData = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            $stmt = $this->conn->prepare($sqlFile);
            $stmt->execute([$materialId]);
            $storageName = $stmt->fetch(PDO::FETCH_ASSOC)['storageName'];
            $stmt->closeCursor();

            return [$materialData, $storageName];
        } catch (PDOException $e) {
            print "Error while getting an specific public material" . $e->getMessage();
        }

        return [];

    }

    public function searchMaterial(string $title) : array {

        $sql = "select pm.id as id, pm.title as title, f.extension as `type` from public_materials as pm join public_materials_files as pmf on pm.id = pmf.public_material join files as f on pmf.file = f.id where pm.title like ?;";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(["%" . $title . "%"]);
            $materials = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $materials;
        } catch (PDOException $e) {
            print "Error when searching materials: " . $e->getMessage();
        }

        return [];

    }

    public function getMaterialsCountAdmin(): int {

        $sql = "select count(*) as total from `public_materials`;";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $materialCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            $stmt->closeCursor();

            return $materialCount;
        } catch (PDOException $e) {
            print "Error getting materials count: " . $e->getMessage();
        }

        return 0;

    }

    public function getRecentMaterials(): array {

        $sql = "select pm.title, f.extension as type, pm.uploaded_date as uploadedDate from public_materials as pm left join public_materials_files as pmf on pm.id = pmf.public_material left join files as f on pmf.file = f.id order by pm.uploaded_date desc limit 4;";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $recentMaterials = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $recentMaterials;
        } catch (PDOException $e) {
            print "Error getting recent materials: " . $e->getMessage();
        }

        return [];

    }

    public function getFileByMaterialAdmin(int $materialId): array {

        $fileSql = "select f.id as fileId, f.original_name as fileOriginalName, size as fileSize from `files` as f join `public_materials_files` as pmf on f.id = pmf.file where pmf.public_material = ?;";
        $materialSql = "select f.storage_name as storageName, pm.uploaded_date as uploadedDate from public_materials as pm join public_materials_files as pmf on pm.id = pmf.public_material join files as f on pmf.file = f.id where pm.id = ?;";

        try {
            $stmt = $this->conn->prepare($fileSql);
            $stmt->execute([$materialId]);
            $fileData = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            $stmt = $this->conn->prepare($materialSql);
            $stmt->execute([$materialId]);
            $materialData = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return [$fileData, $materialData];
        } catch (PDOException $e) {
            print "Error getting file by material: " . $e->getMessage();
        }

        return [];

    }

    public function deleteMaterialFile(int $fileId): bool {
        $sql = "call fDeleteFile(?);";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$fileId]);
            $rowsAffected = $stmt->rowCount();

            return $rowsAffected > 0;
        } catch (PDOException $e) {
            print "Error deleting file: " . $e->getMessage();
        }

        return false;
    }

    public function getMaterialFileId(int $materialId) : ?int {
        $sql = "select `file` as fileId from `public_materials_files` where public_material = ?";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$materialId]);
            $fileId = $stmt->fetch(PDO::FETCH_ASSOC)['fileId'];
            $stmt->closeCursor();

            return $fileId;
        } catch (PDOException $e) {
            print "Error getting material file: " . $e->getMessage();
        }

        return null;
    }

    public function createMaterialFile(int $materialId, string $storageName, string $originalName, string $mime, string $extension, int $size, int $uploaderId) : bool {

        $sql = "call createMaterialFile(?, ?, ?, ?, ?, ?, ?);";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$materialId, $storageName, $originalName, $mime, $extension, $size, $uploaderId]);
            $stmt->closeCursor();

            return true;
        } catch (PDOException $e) {
            print "Error creating material file: " . $e->getMessage();
        }

        return false;

    }

    public function modifyMaterial(int $materialId, string $materialTitle, string $materialDescription) : bool {
        $sql = "call modifyMaterial(?, ?, ?);";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$materialId, $materialTitle, $materialDescription]);
            $stmt->closeCursor();

            return true;
        } catch (PDOException $e) {
            print "Error modifying material: " . $e->getMessage();
        }

        return false;
    }

}

?>