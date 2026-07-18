<?php
// Datoteka: src/Controllers/AuthController.php

class AuthController {
    
    // Prikaz forme za registraciju
    public function showRegisterForm() {
        require_once '../src/Views/register.php';
    }

    // Obrada podataka iz forme za registraciju
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (!empty($name) && !empty($email) && !empty($password)) {
                $db = (new Database())->getConnection();
                $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'client')";
                $stmt = $db->prepare($sql);
                
                // Hashiranje lozinke sa Bcryptom
                $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
                
                header('Location: /login');
                exit;
            }
        }
    }

    // Prikaz forme za prijavu
    public function showLoginForm() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Ako je korisnik već prijavljen, preusmjeri ga na naslovnicu
        if (isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
        
        require_once '../src/Views/login.php';
    }

    // Obrada podataka iz forme za prijavu
    public function login() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $userModel = new User();
            $user = $userModel->findByEmail($email);

            // Provjeravamo postoji li korisnik i podudara li se lozinka
            if ($user && password_verify($password, $user['password'])) {
                // Uspješna prijava: spremamo podatke u sesiju
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role']; // pm, agent, ili client
                
                header('Location: /');
                exit;
            }

            // Ako prijava ne uspije, proslijedi grešku u View
            $error = "Pogrešan email ili lozinka.";
            require_once '../src/Views/login.php';
        }
    }

    // Odjava korisnika
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Uništavamo sesiju i preusmjeravamo na login
        session_destroy();
        header('Location: /login');
        exit;
    }
}