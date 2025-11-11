<?php

require_once("IAssignmentPersistence.php");
require_once("AssignmentPersistence.php");

class AssignmentPersistenceFacade {

    private static ?AssignmentPersistenceFacade $instance = null;

    public function __construct() {

    }

    public static function getInstance() : AssignmentPersistenceFacade {
        if (self::$instance == null) {
            self::$instance = new AssignmentPersistenceFacade();
        }
        return self::$instance;
    }

    public function getIAssignmentPersistence() : IAssignmentPersistence {
        return new AssignmentPersistence();
    }

}

?>