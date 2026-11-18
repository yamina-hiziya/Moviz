<?php

namespace App\Tools;

class Security
{
    /*
        Renvoie le jeton CSRF de la session (le crée s'il n'existe pas encore)
    */
    public static function getCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            // 32 octets aléatoires convertis en texte hexadécimal (64 caractères)
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /*
        Vérifie que le jeton envoyé par le formulaire correspond à celui de la session
    */
    public static function checkCsrfToken(?string $token): bool
    {
        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }
        // hash_equals compare en temps constant pour ne rien laisser deviner du jeton
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}
