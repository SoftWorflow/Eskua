<?php

require('Group.php');
require('User.php');

public class Student {

    private Group $group;

    /*
     *  GETS Y SETS
    */

    public function getGroup() : Group {
        return $this->group;
    }

    public function setGroup(Group $group) : void {
        $this->group = $group;
    }

}

?>