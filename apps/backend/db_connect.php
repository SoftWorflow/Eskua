<?php

class db_connect {
    
    private $host;
    private $db;
    private $user;
    private $pass;
    private $conn;

    public function __construct() {
        $this->host = getenv("DATABASE_HOST");
        $this->db   = getenv("DATABASE_NAME");
        $this->user = getenv("DATABASE_USER");
        $this->pass = getenv("DATABASE_PASSWORD");
    }

    public function connect() {
        $dsn = "mysql:host={$this->host};dbname={$this->db};charset=utf8mb4";
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        ];

        try {
            $this->conn = new PDO($dsn, $this->user, $this->pass, $options);
            
            $this->conn->exec("SET CHARACTER SET utf8mb4");
            $this->conn->exec("SET COLLATION_CONNECTION = utf8mb4_unicode_ci");
            
        } catch (PDOException $e) {
            echo json_encode(['error' => 'DB Connection failed', 'detail' => $e->getMessage()]);
            exit;
        }
        return $this->conn;
    }
}