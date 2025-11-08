<?php

interface IMaterialLogic {
    public function uploadMaterial(PublicMaterial $publicMaterial, ?array $fileData): array;
    public function getAllMaterials() : ?array;
    public function getMaterial(int $materialId) : ?array;
    public function deleteMaterial(int $materialId) :array;
    public function getAllMaterialsAdmin(): array;
    public function getSpecificMaterial(int $materialId): array;
    public function searchMaterial(string $title) : array;
    public function getMaterialsCountAdmin(): int;
    public function getRecentMaterials(): array;
    public function getFileByMaterialAdmin(int $materialId): ?array;
    public function modifyMaterial(int $materialId, string $materialTitle, string $materialDescription, bool $hasChangedFileRaw, ?array $fileData, string $filePath) : array;
    public function getMaterialFileId(int $materialId) : ?array;
    public function createMaterialFile(int $materialId, string $storageName, string $originalName, string $mime, string $extension, int $size, int $uploaderId) : bool;
}

?>