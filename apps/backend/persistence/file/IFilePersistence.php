<?php

interface IFilePersistence {
    public function uploadFile(File $file, int $uploaderId): bool;
}

?>