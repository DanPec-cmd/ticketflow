<?php

namespace App\Core;

class Flash {
    
    /**
     * Dodaje poruku o uspjehu u sesiju.
     */
    public static function success(string $message): void {
        $_SESSION['flash']['success'] = $message;
    }

    /**
     * Dodaje poruku o grešci u sesiju.
     */
    public static function error(string $message): void {
        $_SESSION['flash']['error'] = $message;
    }

    /**
     * Dohvaća i briše određenu vrstu poruke (ako postoji).
     */
    public static function get(string $key): ?string {
        if (isset($_SESSION['flash'][$key])) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $message;
        }
        return null;
    }

    /**
     * Automatski generira HTML (s Tailwindom) za sve dostupne poruke.
     */
    public static function display(): string {
        $output = '';

        if ($success = self::get('success')) {
            $output .= '
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 shadow-sm flex justify-between items-center">
                <span class="text-sm font-medium">✓ ' . htmlspecialchars($success, ENT_QUOTES, 'UTF-8') . '</span>
            </div>';
        }

        if ($error = self::get('error')) {
            $output .= '
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 shadow-sm flex justify-between items-center">
                <span class="text-sm font-medium">⚠ ' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</span>
            </div>';
        }

        return $output;
    }
}