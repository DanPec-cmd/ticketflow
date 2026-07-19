<?php
// Datoteka: src/Core/AuthGuard.php
namespace App\Core;
class AuthGuard {
    
    // Zahtijeva da je korisnik prijavljen
    public static function requireLogin(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }

    // Zahtijeva specifičnu ulogu (npr. PM)
    public static function requireRole(string $role): void {
        self::requireLogin();
        if ($_SESSION['user_role'] !== $role) {
            $_SESSION['error'] = "Nemate dozvolu za ovu akciju.";
            header('Location: /');
            exit;
        }
    }

    // Zahtijeva da korisnik NIJE prijavljen (za Login/Register stranice)
    public static function requireGuest(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
    }
}