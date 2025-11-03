<?php

require_once("IGroupLogic.php");
require_once("GroupLogic.php");

class GroupLogicFacade {

    private static ?GroupLogicFacade $instance = null;

    private function __construct() {

    }

    public static function getInstance() : GroupLogicFacade {
        if (self::$instance == null) {
            self::$instance = new GroupLogicFacade();
        }
        return self::$instance;
    }

    public function getIGroupLogic() : IGroupLogic {
        return new GroupLogic();
    }

}

?>