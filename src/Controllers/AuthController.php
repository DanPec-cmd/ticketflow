<?php
// Datoteka: src/Controllers/AuthController.php
namespace App\Controllers;

use App\Models\User;
use App\Core\AuthGuard;
use App\Core\Validator;
use Exception;

class AuthController {
    
    private User $userModel;
    private $db;

    public function __construct(User $userModel, $dbConnection) {
        $this->userModel = $userModel;
        $this->db = $dbConnection;
    }

    public function showRegisterForm() {
        AuthGuard::requireGuest();
        $_SESSION['csrf_token'] = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));
        require_once '../src/Views/register.php';
    }

    public function register() {
        AuthGuard::requireGuest();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (!Validator::string($name, 2, 100)) {
                $error = "Ime mora sadržavati između 2 i 100 znakova.";
            } elseif (!Validator::email($email)) {
                $error = "Neispravan format email adrese.";
            } elseif (!Validator::string($password, 6)) {
                $error = "Lozinka mora imati barem 6 znakova.";
            }

            if (isset($error)) {
                require_once '../src/Views/register.php';
                return;
            }
            
            $stmtCheck = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmtCheck->execute([$email]);
            if ($stmtCheck->fetch()) {
                $error = "Korisnik s ovom email adresom već postoji.";
                require_once '../src/Views/register.php';
                return;
            }

            $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'client')";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
            
            header('Location: /login');
            exit;
        }
    }

    public function showLoginForm() {
        AuthGuard::requireGuest();
        $_SESSION['csrf_token'] = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));
        require_once '../src/Views/login.php';
    }

    public function login() {
        AuthGuard::requireGuest();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $user = $this->userModel->findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                
                header('Location: /');
                exit;
            }

            $error = "Pogrešan email ili lozinka.";
            require_once '../src/Views/login.php';
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) { 
            session_start(); 
        }
        session_destroy();
        header('Location: /login');
        exit;
    }
}