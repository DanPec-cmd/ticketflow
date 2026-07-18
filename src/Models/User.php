<?php
// Datoteka: src/Models/User.php

class User {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
}