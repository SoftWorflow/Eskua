<?php

require_once("IGroupPersistence.php");
require_once("GroupPersistence.php");

class GroupPersistenceFacade {

    private static ?GroupPersistenceFacade $instance = null;

    private function __construct() {
        
    }

    public static function getInstance() : GroupPersistenceFacade {
        if (self::$instance == null) {
            self::$instance = new GroupPersistenceFacade();
        }
        return self::$instance;
    }

    public function getIGroupPersistence() : IGroupPersistence {
        return new GroupPersistence();
    }

}

?>