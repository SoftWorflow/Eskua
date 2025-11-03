<?php

interface IFileLogic {
    public function uploadFile(File $file, int $uploaderId): bool;
}

?>