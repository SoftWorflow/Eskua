<?php

require_once("IUserLogic.php");
require_once("UserLogic.php");

class UserLogicFacade {
    
    private static ?UserLogicFacade $instance = null;

    private function __construct() {

    }

    public static function getInstance() : UserLogicFacade {
        if (self::$instance == null) {
            self::$instance = new UserLogicFacade();
        }
        return self::$instance;
    }

    public function getIUserLogic() : IUserLogic {
        return new UserLogic();
    }

}

?>