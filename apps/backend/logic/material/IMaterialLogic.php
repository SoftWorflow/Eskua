<?php

interface IMaterialLogic {
    public function uploadMaterial(PublicMaterial $publicMaterial, File $file, int $uploaderId): bool;
}

?>