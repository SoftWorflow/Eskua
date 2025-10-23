<?php

class Group {
    
    private string $name;
    private string $code;
    private User $teacher;

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
    public function getTeacher() : User {
        return $this->teacher;
    }

    public function setTeacher(User $teacher) : void {
        $this->teacher = $teacher;
    }

    /*
     *  CONSTRUCTOR
    */
    public function __construct(string $name, string $code, User $teacher) {
        $this->name = $name;
        $this->code = $code;
        $this->teacher = $teacher;
    }

}

?>