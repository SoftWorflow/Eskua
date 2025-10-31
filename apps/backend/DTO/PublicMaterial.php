<?php

require_once("File.php");

class PublicMaterial {

    private string $title;
    private string $description;
    private File $file;

    public function __construct(string $title, string $description) {
        $this->setTitle($title);
        $this->setDescription($description);
    }

    // TITLE
    public function getTitle(): string {
        return $this->title;
    }

    public function setTitle(string $title): void {
        $this->title = $title;
    }

    // DESCRIPTION
    public function getDescription(): string {
        return $this->description;
    }

    public function setDescription(string $description): void {
        $this->description = $description;
    }

    // FILE
    public function getFile(): File {
        return $this->file;
    }

    public function setFile(File $file): void {
        $this->file = $file;
    }

}

?>