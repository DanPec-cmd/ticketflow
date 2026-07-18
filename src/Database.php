<?php
// Datoteka: src/Database.php

class Database {
    private $host = '127.0.0.1';
    private $db   = 'elatus_tickets';
    private $user = 'root';  //username za bazu
    private $pass = '';     // password za bazu, kod mene je prazno
    private $charset = 'utf8mb4';
    private $pdo;

    public function __construct() {
        $dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset";
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Baza ovo radi brže od PHP-a
        ];
        
        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (\PDOException $e) {
            die("Greška pri spajanju na bazu: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->pdo;
    }
}