<?php
// Datoteka: src/Database.php

class Database {
    private $host;
    private $db;
    private $user;
    private $pass;
    private $charset = 'utf8mb4';
    private $pdo;

    public function __construct() {
        // Učitavanje iz varijabli okruženja
        $this->host = getenv('DB_HOST');
        $this->db   = getenv('DB_NAME');
        $this->user = getenv('DB_USER');
        $this->pass = getenv('DB_PASS');

        $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       
            PDO::ATTR_EMULATE_PREPARES   => false,                  
        ];
        
        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (\PDOException $e) {
            // U produkciji je bolje logirati grešku umjesto ispisivanja e->getMessage()
            die("Greška pri spajanju na bazu.");
        }
    }

    public function getConnection() {
        return $this->pdo;
    }
}