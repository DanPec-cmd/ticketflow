<?php
// Datoteka: src/Controllers/UserController.php
namespace App\Controllers;

use App\Models\User;
use App\Core\AuthGuard;

class UserController {
    
    private User $userModel;

    public function __construct(User $userModel) {
        $this->userModel = $userModel;
        
        // Zabrani pristup svima osim PM-u!
        AuthGuard::requireRole('pm');
    }

    // Prikazuje listu svih korisnika
    public function index() {
        $users = $this->userModel->getAllUsers();
        require_once __DIR__ . '/../Views/users.php';
    }

    // Obrađuje promjenu uloge
    public function updateRole() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'] ?? null;
            $newRole = $_POST['role'] ?? null;

            // Dozvoljene uloge u sustavu (prilagodi ako ih imaš više)
            $allowedRoles = ['client', 'agent', 'pm'];

            if ($userId && in_array($newRole, $allowedRoles)) {
                
                // Sigurnosna provjera: Ne daj PM-u da slučajno sebi skine PM ulogu i zaključa se van sustava
                if ($userId == $_SESSION['user_id']) {
                    $_SESSION['error'] = "Ne možete promijeniti vlastitu ulogu.";
                } else {
                    $this->userModel->updateRole($userId, $newRole);
                    $_SESSION['message'] = "Uloga korisnika je uspješno ažurirana.";
                }
            } else {
                $_SESSION['error'] = "Nevažeći podaci za ulogu.";
            }
            
            header('Location: /users');
            exit;
        }
    }
}