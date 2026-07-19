<?php
// Datoteka: src/Models/User.php
namespace App\Models;

use PDO; 

class User {
    private $db;

    // Konstruktor sada prima PDO konekciju putem Dependency Injection-a
    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }
}