<?php
// Datoteka: src/Controllers/UserController.php
namespace App\Controllers;

use App\Models\User;
use App\Core\AuthGuard;
use App\Core\Flash;

class UserController {
    
    private User $userModel;

    public function __construct(User $userModel) {
        $this->userModel = $userModel;
        
        // Zabrani pristup svima osim PM-u!
        AuthGuard::requireRole('pm');
    }

    /**
     * Prikazuje listu svih korisnika
     */
    public function index() {
        $users = $this->userModel->getAllUsers();
        require_once __DIR__ . '/../Views/users.php';
    }

    /**
     * Obrađuje promjenu uloge korisnika
     */
    public function updateRole() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /users');
            exit;
        }

        // 1. Sigurnosna provjera: CSRF Token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (empty($csrfToken) || $csrfToken !== ($_SESSION['csrf_token'] ?? '')) {
            Flash::error("Sigurnosna provjera nije uspjela (Neispravan CSRF token).");
            header('Location: /users');
            exit;
        }

        $userId = $_POST['user_id'] ?? null;
        $newRole = $_POST['role'] ?? null;

        // Dozvoljene uloge u sustavu
        $allowedRoles = ['client', 'agent', 'pm'];

        // 2. Provjera valjanosti podataka
        if (!$userId || !in_array($newRole, $allowedRoles)) {
            Flash::error("Nevažeći podaci za ulogu.");
            header('Location: /users');
            exit;
        }
        
        // 3. Sigurnosna provjera: Ne daj PM-u da sam sebe zaključa
        if ($userId == $_SESSION['user_id']) {
            Flash::error("Ne možete promijeniti vlastitu ulogu.");
            header('Location: /users');
            exit;
        } 

        // 4. Izvršavanje akcije u bazi
        if ($this->userModel->updateRole($userId, $newRole)) {
            Flash::success("Uloga korisnika je uspješno ažurirana.");
        } else {
            Flash::error("Došlo je do greške prilikom ažuriranja uloge u bazi.");
        }
        
        header('Location: /users');
        exit;
    }
}