<?php
// Datoteka: public/index.php

// Dodano da mi prikazuje sve greške, maknuti prije produkcije!!!
ini_set('display_errors', 1); error_reporting(E_ALL);

session_start();

// Autoloader
spl_autoload_register(function ($class) {
    // Ako se bude proširivalo da php i ja znamo gdje je sve
    $paths = [
        '../src/' . $class . '.php',
        '../src/Controllers/' . $class . '.php',
        '../src/Models/' . $class . '.php',
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

$router = new Router();

// Rute se nalaze ovdje
$router->add('GET', '/', 'TicketController', 'index');
$router->add('GET', '/tickets/create', 'TicketController', 'create');
$router->add('POST', '/tickets/store', 'TicketController', 'store');
$router->add('GET', '/ticket', 'TicketController', 'show');
$router->add('POST', '/ticket/reply', 'TicketController', 'addReply');

// Login rute 
$router->add('GET', '/login', 'AuthController', 'showLoginForm');
$router->add('POST', '/login/submit', 'AuthController', 'login');
$router->add('GET', '/logout', 'AuthController', 'logout');

// Kreiranje novog korisnika
$router->add('GET', '/register', 'AuthController', 'showRegisterForm');
$router->add('POST', '/register/submit', 'AuthController', 'register');

// Dodjeljivanje ticketa (PM akcija)
$router->add('POST', '/ticket/assign', 'TicketController', 'assign');

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);