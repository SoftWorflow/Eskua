<?php

require('User.php');

public class Teacher extends User {

    public function __construct(string $username, string $email, string $display_name, string $profile_picture_url, string $password, UserRole $userRole) {
        parent::__construct($username, $email, $display_name, $profile_picture_url, $password, $userRole);
    }

}

?>