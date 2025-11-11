<?php

interface IGroupLogic {
    public function createGroup(Group $group) : bool;
    public function getGroupByCode($code) : ?array;
    public function deactivateAssignment(int $assignmentId) : array;
    public function getAllGroupsCountAdmin(): int;
    public function getAllGroupsAdmin(): array;
    public function getSpecificGroupDataAdmin(int $groupId) : array;
    public function searchGroupsByTeacherNameAdmin(string $teacherName) : array;
    public function getGroupMembers(int $groupId) : array;
    public function getGroup(int $groupId) : array;
}

?>