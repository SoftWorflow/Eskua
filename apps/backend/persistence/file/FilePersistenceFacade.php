<?php

require_once("IFilePersistence.php");
require_once("FilePersistence.php");

class FilePersistenceFacade {

    private static ?FilePersistenceFacade $instance = null;

    private function __construct() {

    }

    public static function getInstance() : FilePersistenceFacade {
        if (self::$instance == null) {
            self::$instance = new FilePersistenceFacade();
        }
        return self::$instance;
    }

    public function getIFilePersistence() : IFilePersistence {
        return new FilePersistence();
    }

}

?>