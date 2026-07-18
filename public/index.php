<?php
// Datoteka: public/index.php

// Dodano da mi prikazuje sve greške, maknuti prije produkcije!!!
ini_set('display_errors', 1); error_reporting(E_ALL);

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

//Rute se nalaze ovdje
$router->add('GET', '/', 'TicketController', 'index');
$router->add('GET', '/tickets/create', 'TicketController', 'create');
$router->add('POST', '/tickets/store', 'TicketController', 'store');
$router->add('GET', '/ticket', 'TicketController', 'show');
$router->add('POST', '/ticket/reply', 'TicketController', 'addReply');


$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);