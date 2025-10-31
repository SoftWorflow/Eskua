<?php

class File {

    private string $originalName;
    private string $storageName;
    private string $mime;
    private string $extension;
    private string $size;
    public const FILE_PATH = '/var/www/html/uploads';
    public const MAX_SIZE = 20 * 1024 * 1024; // 20 MB
    public const ALLOWED_MIME = [
        'application/pdf',
        'image/png',
        'image/jpeg',
        'image/webp',
        'video/mp4'
    ];
    public const ALLOWED_EXTENSIONS = ['pdf','png','jpg','jpeg','webp', 'mp4'];

    public function __construct(string $originalName, string $storageName, string $mime, string $extension, string $size) {
        $this->setOriginalName($originalName);
        $this->setStorageName($storageName);
        $this->setMime($mime);
        $this->setExtension($extension);
        $this->setSize($size);
    }

    // ORIGINAL NAME
    public function getOriginalName(): string {
        return $this->originalName;
    }

    public function setOriginalName(string $originalName): void {
        $this->originalName = $originalName;
    }

    // STORAGE NAME
    public function getStorageName(): string {
        return $this->storageName;
    }

    public function setStorageName(string $storageName): void {
        $this->storageName = $storageName;
    }

    // MIME
    public function getMime(): string {
        return $this->mime;
    }

    public function setMime(string $mime): void {
        $this->mime = $mime;
    }

    // EXTENSION
    public function getExtension(): string {
        return $this->extension;
    }

    public function setExtension(string $extension): void {
        $this->extension = $extension;
    }

    // SIZE
    public function getSize(): string {
        return $this->size;
    }

    public function setSize(string $size): void {
        $this->size = $size;
    }

}

?>