<?php
// Datoteka: src/Controllers/AuthController.php
namespace App\Controllers;

use App\Models\User;
use App\Core\AuthGuard;
use App\Core\Validator;
use App\Core\Flash;
use Exception;

class AuthController {
    
    private User $userModel;
    private $db;

    public function __construct(User $userModel, $dbConnection) {
        $this->userModel = $userModel;
        $this->db = $dbConnection;
    }

    /**
     * Prikazuje formu za registraciju
     */
    public function showRegisterForm() {
        AuthGuard::requireGuest();
        $_SESSION['csrf_token'] = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));
        require_once '../src/Views/register.php';
    }

    /**
     * Obrađuje zahtjev za registraciju
     */
    public function register() {
        AuthGuard::requireGuest();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /register');
            exit;
        }

        // 1. CSRF Zaštita
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (empty($csrfToken) || $csrfToken !== ($_SESSION['csrf_token'] ?? '')) {
            Flash::error("Sigurnosna provjera nije uspjela (Neispravan CSRF token).");
            header('Location: /register');
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // 2. Validacija podataka
        if (!Validator::string($name, 2, 100)) {
            Flash::error("Ime mora sadržavati između 2 i 100 znakova.");
            header('Location: /register');
            exit;
        } 
        
        if (!Validator::email($email)) {
            Flash::error("Neispravan format email adrese.");
            header('Location: /register');
            exit;
        } 
        
        if (!Validator::string($password, 6)) {
            Flash::error("Lozinka mora imati barem 6 znakova.");
            header('Location: /register');
            exit;
        }

        // 3. Provjera postoji li već korisnik
        $stmtCheck = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmtCheck->execute([$email]);
        if ($stmtCheck->fetch()) {
            Flash::error("Korisnik s ovom email adresom već postoji.");
            header('Location: /register');
            exit;
        }

        // 4. Unos u bazu podataka
        $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'client')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
        
        Flash::success("Uspješno ste se registrirali! Sada se možete prijaviti.");
        header('Location: /login');
        exit;
    }

    /**
     * Prikazuje formu za prijavu
     */
    public function showLoginForm() {
        AuthGuard::requireGuest();
        $_SESSION['csrf_token'] = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));
        require_once '../src/Views/login.php';
    }

    /**
     * Obrađuje zahtjev za prijavu
     */
    public function login() {
        AuthGuard::requireGuest();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /login');
            exit;
        }

        // 1. CSRF Zaštita
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (empty($csrfToken) || $csrfToken !== ($_SESSION['csrf_token'] ?? '')) {
            Flash::error("Sigurnosna provjera nije uspjela (Neispravan CSRF token).");
            header('Location: /login');
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $user = $this->userModel->findByEmail($email);

        // 2. Autentifikacija
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            
            Flash::success("Dobrodošli natrag, " . htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') . "!");
            header('Location: /');
            exit;
        }

        // Ako prijava ne uspije
        Flash::error("Pogrešan email ili lozinka.");
        header('Location: /login');
        exit;
    }

    /**
     * Odjava korisnika iz sustava
     */
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) { 
            session_start(); 
        }
        
        session_destroy();
        
        // Budući da session_destroy briše apsolutno sve, moramo pokrenuti novu praznu sesiju
        // kako bi naša Flash poruka o uspješnoj odjavi mogla preživjeti do login stranice.
        session_start();
        Flash::success("Uspješno ste se odjavili iz sustava.");
        
        header('Location: /login');
        exit;
    }
}