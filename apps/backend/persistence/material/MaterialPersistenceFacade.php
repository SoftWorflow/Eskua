<?php

require_once("IMaterialPersistence.php");
require_once("MaterialPersistence.php");

class MaterialPersistenceFacade {

    private static ?MaterialPersistenceFacade $instance = null;

    private function __construct() {

    }

    public static function getInstance() : MaterialPersistenceFacade {
        if (self::$instance == null) {
            self::$instance = new MaterialPersistenceFacade();
        }
        return self::$instance;
    }

    public function getIMaterialPersistence() : IMaterialPersistence {
        return new MaterialPersistence();
    }

}

?>