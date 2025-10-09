<?php

require('Teacher.php');

class Group {
    
    private string $name;
    private string $code;
    private Teacher $teacher;

    /*
     *  GETS Y SETS
    */

    // Name
    public function getName() : string {
        return $this->name;
    }

    public function setName(string $name) : void {
        $this->name = $name;
    }

    // Code
    public function getCode() : string {
        return $this->code;
    }

    public function setCode(string $code) : void {
        $this->code = $code;
    }

    // Teacher
    public function getTeacher() : Teacher {
        return $this->teacher;
    }

    public function setTeacher(Teacher $teacher) : void {
        $this->teacher = $teacher;
    }

    /*
     *  CONSTRUCTOR
    */
    public function __construct(string $name, string $code, Teacher $teacher) {
        $this->name = $name;
        $this->code = $code;
        $this->teacher = $teacher;
    }

}

?>