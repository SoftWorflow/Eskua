<?php

require('UserRole.php');

class User {

    private string $username;
    private string $email;
    private string $display_name;
    private string $profile_picture_url;
    private string $password;
    private string $userRole;

    /*
     *  GETS Y SETS
    */

    // Username
    public function getUsername() : string {
        return $this->username;
    }

    public function setUsername(string $username) : void {
        $this->username = $username;
    }

    // Email
    public function getEmail() : string {
        return $this->email;
    }

    public function setEmail(string $email) : void {
        $this->email = $email;
    }

    // Display name
    public function getDisplayName() : string {
        return $this->display_name;
    }

    public function setDisplayName(string $display_name) : void {
        $this->display_name = $display_name;
    }

    // Profile picture url
    public function getProfilePictureUrl() : string {
        return $this->profile_picture_url;
    }

    public function setProfilePictureUrl(string $profile_picture_url) : void {
        $this->profile_picture_url = $profile_picture_url;
    }

    // Password
    public function getPassword() : string {
        return $this->password;
    }

    public function setPassword(string $password) : void {
        $this->password = $password;
    }

    // User role
    public function getUserRole() : string {
        return $this->userRole;
    }

    public function setUserRole(string $userRole) : void {
        if (!UserRole::isValid($userRole)) {
            throw new InvalidArgumentException("Invalid role: $userRole");
        }
        $this->userRole = $userRole;
    }

    /*
     *  CONSTRUCTOR
    */
    public function __construct(string $username, string $email, string $display_name, string $profile_picture_url, string $password, string $userRole) {
        $this->username = $username;
        $this->email = $email;
        $this->display_name = $display_name;
        $this->profile_picture_url = $profile_picture_url;
        $this->password = $password;
        $this->userRole = $userRole;
        $this->setUserRole($userRole);
    }

}

?>