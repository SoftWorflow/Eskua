<?php

require_once("IMaterialLogic.php");
require_once(__DIR__ . "/../../persistence/material/MaterialPersistenceFacade.php");

class MaterialLogic implements IMaterialLogic {
    public function uploadMaterial(PublicMaterial $publicMaterial, File $file, int $uploaderId): bool {
        if (!isset($publicMaterial) || !isset($file) || $uploaderId === null) return false;

        $materialPersistence = MaterialPersistenceFacade::getInstance()->getIMaterialPersistence();

        return $materialPersistence->uploadMaterial($publicMaterial, $file, $uploaderId);
    }

    public function getAllMaterials() : ?array {
        $materialPersistence = MaterialPersistenceFacade::getInstance()->getIMaterialPersistence();

        return $materialPersistence->getAllMaterials();
    }

    public function getMaterial(int $materialId) : ?array {
        if ($materialId === null) return null;

        $materialPersistence = MaterialPersistenceFacade::getInstance()->getIMaterialPersistence();

        return $materialPersistence->getMaterial($materialId);
    }
}

?>