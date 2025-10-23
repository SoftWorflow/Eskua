<?php

require_once("IGroupLogic.php");
require_once(__DIR__ . "/../../DTO/Group.php");
require_once(__DIR__ . "/../../persistence/group/GroupPersistenceFacade.php");

class GroupLogic implements IGroupLogic {

    public function createGroup(Group $group) : bool {
        if ($group === null) return false;
    }

    public function getGroupByCode($code) : ?array {
        if (empty($code)) return null;

        $groupPersistence = GroupPersistenceFacade::getInstance()->getIGroupPersistence();
        $dbGroup = $groupPersistence->getGroupByCode($code);

        if ($dbGroup === null) echo "No hay grupo";

        $id = $dbGroup[0];
        $group = $dbGroup[1];

        return [$id, $group];
    }

}

?>