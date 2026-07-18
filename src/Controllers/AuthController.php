<?php
// Datoteka: src/Controllers/AuthController.php

class AuthController {
    
    // Prikaz forme za prijavu
    public function showLoginForm() {
        // Ako je već prijavljen, makni ga s login stranice
        if (isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
        require_once '../src/Views/login.php';
    }

    // Obrada podataka iz forme
    public function login() {
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
                $_SESSION['user_role'] = $user['role'];
                
                header('Location: /');
                exit;
            }

            // Ako prijava ne uspije, vraćamo ga na formu s greškom
            $error = "Pogrešan email ili lozinka.";
            require_once '../src/Views/login.php';
        }
    }

    // Odjava
    public function logout() {
        session_destroy();
        header('Location: /login');
        exit;
    }
}