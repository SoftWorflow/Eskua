<?php

require_once("IAssignmentLogic.php");
require_once("AssignmentLogic.php");

class AssignmentLogicFacade {

    private static ?AssignmentLogicFacade $instance = null;

    public function __construct() {

    }

    public static function getInstance() : AssignmentLogicFacade {
        if (self::$instance == null) {
            self::$instance = new AssignmentLogicFacade();
        }
        return self::$instance;
    }

    public function getIAssignmentLogic() : IAssignmentLogic {
        return new AssignmentLogic();
    }

}

?>