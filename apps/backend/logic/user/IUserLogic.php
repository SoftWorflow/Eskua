<?php

interface IUserLogic {
    public function createUser(User $user) : bool;
    public function createStudent(User $user, $groupId) : bool;
    public function deleteUserById(int $id) : array;
    public function deleteUserByUsername(string $username) : bool;
    public function modifyUser(int $id, User $user) : bool;
    public function getUserById(?int $userId = null) : ?array;
    public function getUserByUsername(string $username) : ?array;
    public function getUserByEmail(string $email) : ?array;
    public function generateToken(User $user) : ?array;
    public function refreshToken() : ?array;
    public function revokeRefreshToken($refreshToken) : bool;
    public function getStudentGroup() : ?array;
    public function getTeacherGroups(int $userId) : array;
    public function getAssignmentsFromGroup(int $groupId) : array;
    public function getAllUsersAdmin(): array;  
    public function getSpecificUserData(int $userId) : array;
    public function searchUsers(string $username) : array;
    public function getAllUsersCountAdmin() : array;
    public function login(string $username, string $password) : array;
    public function logout() : array;
    public function register(string $username, string $email, string $displayName, string $password, string $confirmPassword, string $userRole, ?string $groupCode = null) : array;
}

?>