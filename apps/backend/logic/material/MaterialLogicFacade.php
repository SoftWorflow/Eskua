<?php

require_once("IMaterialLogic.php");
require_once("MaterialLogic.php");

class MaterialLogicFacade {

    private static ?MaterialLogicFacade $instance = null;

    public function __construct() {

    }

    public static function getInstance() : MaterialLogicFacade {
        if (self::$instance == null) {
            self::$instance = new MaterialLogicFacade();
        }
        return self::$instance;
    }

    public function getIMaterialLogic() : IMaterialLogic {
        return new MaterialLogic();
    }

}

?>