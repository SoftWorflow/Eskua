<?php

class GroupAssignment {
    private string $name;
    private string $description;
    private int $maxScore;
    private int $groupId;

    public function __construct(string $name, string $description, int $maxScore, int $groupId) {
        $this->setName($name);
        $this->setDescription($description);
        $this->setMaxScore($maxScore);
        $this->setGroupId($groupId);
    }

    // NAME
    public function getName(): string {
        return $this->name;
    }
    public function setName(string $name): void {
        $this->name = $name;
    }

    // DESCRIPTION
    public function getDescription(): string {
        return $this->description;
    }

    public function setDescription(string $description): void {
        $this->description = $description;
    }

    // MAX SCORE
    public function getMaxScore(): int {
        return $this->maxScore;
    }

    public function setMaxScore(int $maxScore): void {
        $this->maxScore = $maxScore;
    }

    // GROUP ID
    public function getGroupId(): int {
        return $this->groupId;
    }

    public function setGroupId(int $groupId): void {
        $this->groupId = $groupId;
    }
}

?>