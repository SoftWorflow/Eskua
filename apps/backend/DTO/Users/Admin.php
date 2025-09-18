<?php

require('User.php');

class Admin extends User {

    /*
     *  CONSTRUCTOR
    */
    public function __construct(string $username, string $email, string $display_name, string $profile_picture_url, string $password, string $userRole) {
        parent::__construct($username, $email, $display_name, $profile_picture_url, $password, $userRole);
    }

}

?>