<?php
// Datoteka: src/Models/User.php
namespace App\Models;

use PDO; 

class User {
    private $db;

    // Konstruktor prima PDO konekciju putem Dependency Injection-a
    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Pronalazi korisnika na temelju email adrese.
     */
    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    /**
     * Dohvaća sve korisnike iz baze.
     */
    public function getAllUsers() {
        $stmt = $this->db->query("SELECT id, name, email, role FROM users ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ažurira ulogu određenog korisnika.
     */
    public function updateRole($id, $role) {
        $stmt = $this->db->prepare("UPDATE users SET role = ? WHERE id = ?");
        return $stmt->execute([$role, $id]);
    }
}