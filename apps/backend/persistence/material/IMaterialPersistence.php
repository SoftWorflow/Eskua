<?php

interface IMaterialPersistence {
    public function uploadMaterial(PublicMaterial $publicMaterial, File $file, int $uploaderId): bool;
    public function getAllMaterials() : ?array;
    public function getMaterial(int $materialId): ?array;
}

?>