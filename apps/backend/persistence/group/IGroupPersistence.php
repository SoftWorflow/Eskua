<?php

interface IGroupPersistence {
    public function createGroup(Group $group) : bool;
    public function getGroupByCode($code) : ?array;
}

?>