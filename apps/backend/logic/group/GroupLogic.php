<?php

require_once(__DIR__ . "/../../middleware/auth.php");

require_once("IGroupLogic.php");
require_once(__DIR__ . "/../../DTO/Group.php");
require_once(__DIR__ . "/../../persistence/group/GroupPersistenceFacade.php");

class GroupLogic implements IGroupLogic {

    public function createGroup(Group $group) : bool {
        if ($group === null) return false;
        return true;
    }

    public function getGroupByCode($code) : ?array {
        if (empty($code)) return null;

        $groupPersistence = GroupPersistenceFacade::getInstance()->getIGroupPersistence();
        $dbGroup = $groupPersistence->getGroupByCode($code);

        if ($dbGroup === null) return null;

        $id = $dbGroup[0];
        $group = $dbGroup[1];

        return [$id, $group];
    }

    public function getGroup(int $groupId) : array {
        if ($groupId === null || empty($groupId)) {
            return ['ok' => false, 'error' => 'No se recibió el identificador del grupo'];
        }

        $groupPersistence = GroupPersistenceFacade::getInstance()->getIGroupPersistence();

        $group = $groupPersistence->getGroup($groupId);

        if (empty($group)) {
            return ['ok' => false, 'error' => 'No se encontró el grupo'];
        }

        return ['ok' => true, 'group' => $group];
    }

    public function deactivateAssignment(int $assignmentId) : array {
        AuthMiddleware::authorize(['admin', 'teacher']);

        if (empty($assignmentId) || $assignmentId === null) {
            http_response_code(400);
            return ['ok' => false, 'error' => 'No se recibió el identificador de la tarea'];
        }

        $groupPersistence = GroupPersistenceFacade::getInstance()->getIGroupPersistence();

        $result = $groupPersistence->deactivateAssignment($assignmentId);

        if (!$result) {
            return ['ok' => false, 'error' => 'Hubo un error al desactivar la tarea'];
        }

        return ['ok' => true, 'message' => 'El material se ha desactivado correctamente'];
    }

    public function getAllGroupsCountAdmin(): int {
        AuthMiddleware::authorize(['admin']);

        $groupPersistence = GroupPersistenceFacade::getInstance()->getIGroupPersistence();

        $groups = $groupPersistence->getAllGroupsCountAdmin();

        return $groups;
    }

    public function getAllGroupsAdmin(): array {
        AuthMiddleware::authorize(['admin']);

        $groupPersistence = GroupPersistenceFacade::getInstance()->getIGroupPersistence();

        $groups = $groupPersistence->getAllGroupsAdmin();

        if (empty($groups)) {
            return ['ok' => false, 'message' => 'No hay grupos'];
        }

        return $groups;
    }

    public function getSpecificGroupDataAdmin(int $groupId) : array {
        AuthMiddleware::authorize(['admin']);

        if (empty($groupId) || $groupId === null || $groupId < 0) {
            return ['ok' => false, 'message' => 'No se recibió el identificador del grupo'];
        }

        $groupPersistence = GroupPersistenceFacade::getInstance()->getIGroupPersistence();

        $groupData = $groupPersistence->getSpecificGroupData($groupId);

        return $groupData;
    }

    public function searchGroupsByTeacherNameAdmin(string $teacherName) : array {
        AuthMiddleware::authorize(['admin']);

        $groupPersistence = GroupPersistenceFacade::getInstance()->getIGroupPersistence();

        $groupsData = $groupPersistence->searchGroupsByTeacherNameAdmin($teacherName);

        if (empty($groupsData)) {
            return ['ok' => false, 'message' => 'No se encontraron resultados'];
        }

        return ['ok' => true, $groupsData];
    }

    public function getGroupMembers(int $groupId) : array {
        AuthMiddleware::authorize(['teacher', 'student']);
        
        if ($groupId === null || empty($groupId) || $groupId < 0) {
            return ['ok' => false, 'error' => 'No se recibió el identificador del grupo'];
        }

        $groupPersistence = GroupPersistenceFacade::getInstance()->getIGroupPersistence();

        $members = $groupPersistence->getGroupMembers($groupId);

        return ['ok' => true, 'members' => $members];
    }

}

?>