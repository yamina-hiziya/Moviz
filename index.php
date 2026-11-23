<?php
require_once __DIR__ . '/config.php';

// Sécurise le cookie de session
session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/',
    'domain' => $_SERVER['SERVER_NAME'],
    // Le cookie n'est pas lisible en JavaScript (protège contre le vol de session par XSS)
    'httponly' => true,
    // Le cookie n'est envoyé qu'en HTTPS quand le site est en HTTPS
    'secure' => !empty($_SERVER['HTTPS']),
    // Le cookie n'est pas envoyé quand un autre site soumet un formulaire vers le nôtre (protège contre le CSRF)
    'samesite' => 'Lax'
]);
session_start();
define('_ROOTPATH_', __DIR__);
define('_TEMPLATEPATH_', __DIR__ . '/templates');
spl_autoload_register(function (string $class) {
    $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

use App\Controller\Controller;

$controller = new Controller();
$controller->route();
