<?php
// Datoteka: src/Core/Validator.php
namespace App\Core;

class Validator {
    
    /**
     * Provjerava je li string unutar zadanih granica (uključujući i trimanje).
     * INF (Infinity) znači da po defaultu nema gornje granice.
     */
    public static function string($value, $min = 1, $max = INF) {
        $value = trim($value);
        $length = strlen($value);
        
        return $length >= $min && $length <= $max;
    }

    /**
     * Provjerava je li unesena vrijednost ispravan email format.
     */
    public static function email($value) {
        return filter_var(trim($value), FILTER_VALIDATE_EMAIL) !== false;
    }
}