<?php

interface IMaterialPersistence {
    public function uploadMaterial(PublicMaterial $publicMaterial, File $file, int $uploaderId): bool;
    public function getAllMaterials() : ?array;
    public function getMaterial(int $materialId): ?array;
    public function getAllMaterialsAdmin(): array;
    public function getSpecificMaterial(int $materialId): array;
    public function deleteMaterial(int $materialId): bool;
    public function searchMaterial(string $title) : array;
    public function getMaterialsCountAdmin(): int;
    public function getRecentMaterials(): array;
    public function getFileByMaterialAdmin(int $materialId): array;
    public function deleteMaterialFile(int $fileId): bool;
    public function getMaterialFileId(int $materialId) : ?int;
    public function createMaterialFile(int $materialId, string $storageName, string $originalName, string $mime, string $extension, int $size, int $uploaderId) : bool;
    public function modifyMaterial(int $materialId, string $materialTitle, string $materialDescription) : bool;
}

?>