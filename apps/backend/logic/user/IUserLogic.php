<?php

interface IUserLogic {
    public function createUser(User $user) : bool;
    public function createStudent(User $user, $groupId) : bool;
    public function deleteUserById(int $id) : bool;
    public function deleteUserByUsername(string $username) : bool;
    public function modifyUser(int $id, User $user) : bool;
    
    public function getUserById(int $id) : ?array;
    public function getUserByUsername(string $username) : ?array;
    public function getUserByEmail(string $email) : ?array;

    public function generateToken(User $user) : ?array;
    public function refreshToken() : ?array;
    public function revokeRefreshToken($refreshToken) : bool;
}

?>