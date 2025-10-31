<?php

require_once("IFileLogic.php");
require_once("FileLogic.php");

class FileLogicFacade {

    private static ?FileLogicFacade $instance = null;

    public function __construct() {

    }

    public static function getInstance() : FileLogicFacade {
        if (self::$instance == null) {
            self::$instance = new FileLogicFacade();
        }
        return self::$instance;
    }

    public function getIFileLogic() : IFileLogic {
        return new FileLogic();
    }

}

?>