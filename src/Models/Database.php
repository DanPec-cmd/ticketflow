<?php
// Datoteka: src/Models/Database.php

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;

    public function __construct() {
        // Povlačimo podatke iz globalnog $_ENV niza koji smo učitali u index.php
        // Dodajemo "fallback" vrijednosti za svaki slučaj ako .env varijable nedostaju samo za test potrebe
        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->db_name = $_ENV['DB_NAME'] ?? 'elatus_tickets';
        $this->username = $_ENV['DB_USER'] ?? 'root';
        $this->password = $_ENV['DB_PASS'] ?? '';
    }

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4", 
                $this->username, 
                $this->password
            );
            
            // Postavljanje PDO-a da baca iznimke u slučaju greške
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Vraćanje rezultata u obliku asocijativnog niza za lakši rad s podacima
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        } catch(PDOException $exception) {
            // 1. Zapisujemo stvarnu tehničku grešku u pozadinski PHP error log
            error_log("Database Connection Error: " . $exception->getMessage());
            
            // 2. Zaustavljamo izvođenje koda i korisniku prikazujemo generičku poruku
            die("Došlo je do tehničkog problema sa sustavom. Molimo pokušajte malo kasnije.");
            // Umjesto die("Došlo je do tehničkog problema..."); za potrebe testiranja
            //die("Detaljna greška: " . $exception->getMessage());
        }

        return $this->conn;
    }
}