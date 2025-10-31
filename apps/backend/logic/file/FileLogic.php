<?php

require_once("IFileLogic.php");
require_once(__DIR__ . "/../../DTO/File.php");
require_once(__DIR__ . "/../../persistence/file/FilePersistenceFacade.php");

class FileLogic implements IFileLogic {

    public function uploadFile(File $file, int $uploaderId): bool {
        if (!isset($file)) return false;

        $filePersistence = FilePersistenceFacade::getInstance()->getIFilePersistence();

        return $filePersistence->uploadFile($file, $uploaderId);
    }

}

?>