<?php

interface IUserPersistence {
    public function createUser(User $user) : bool;
    public function addGroupToStudent($userId, $groupId) : bool;
    public function deleteUser(int $id) : bool;
    public function modifyUser(int $id, User $user) : bool;
    
    public function getUserById(int $id) : ?array;
    public function getUserByUsername(string $username) : ?array;
    public function getUserByEmail(string $email) : ?array;

    public function createRefreshToken(int $id, string $refreshToken, string $refreshExpire) : bool;
    public function getRefreshToken(string $refreshToken) : ?array;
    public function revokeRefreshToken($refreshToken) : bool;
}

?>