<?php

require_once("IUserPersistence.php");
require_once("UserPersistence.php");

class UserPersistenceFacade {

    private static ?UserPersistenceFacade $instance = null;

    private function __construct() {
        
    }

    public static function getInstance() : UserPersistenceFacade {
        if (self::$instance == null) {
            self::$instance = new UserPersistenceFacade();
        }
        return self::$instance;
    }

    public function getIUserPersistence() : IUserPersistence {
        return new UserPersistence();
    }

}

?>