<?php
// Datoteka: public/index.php (ili root index.php ovisno o strukturi)

session_start();

// 1. UČITAVANJE .ENV DATOTEKE (Ovo omogućuje da Database.php pročita lozinke)
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Preskoči komentare
        if (strpos(trim($line), '#') === 0) continue;
        
        // Razdvoji ključ i vrijednost i spremi u globalni $_ENV niz
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $_ENV[trim($name)] = trim($value);
        }
    }
}

// 2. Uključivanje osnovnih klasa
require_once __DIR__ . '/../src/Core/Container.php';
require_once __DIR__ . '/../src/Core/AuthGuard.php';
require_once __DIR__ . '/../src/Models/Database.php';
require_once __DIR__ . '/../src/Models/User.php';
require_once __DIR__ . '/../src/Models/Ticket.php';
require_once __DIR__ . '/../src/Controllers/AuthController.php';
require_once __DIR__ . '/../src/Controllers/TicketController.php';

// 3. Inicijalizacija Service Containera
$container = new Container();

// Registracija zavisnosti (Bindings)
$container->bind('Database', function($c) {
    return (new Database())->getConnection();
});

$container->bind('User', function($c) {
    return new User();
});

$container->bind('Ticket', function($c) {
    return new Ticket();
});

$container->bind('AuthController', function($c) {
    return new AuthController($c->get('User'), $c->get('Database'));
});

$container->bind('TicketController', function($c) {
    return new TicketController($c->get('Ticket'));
});

// 4. Jednostavno rutiranje (Routing)
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Uklanjanje trailing slasha za čišće rute
if ($uri !== '/' && substr($uri, -1) === '/') {
    $uri = rtrim($uri, '/');
}

// Mapiranje ruta na kontrolere
switch ($uri) {
    // Autentifikacija
    case '/login':
        $controller = $container->get('AuthController');
        $controller->showLoginForm();
        break;
    case '/login/submit':
        $controller = $container->get('AuthController');
        $controller->login();
        break;
    case '/register':
        $controller = $container->get('AuthController');
        $controller->showRegisterForm();
        break;
    case '/register/submit':
        $controller = $container->get('AuthController');
        $controller->register();
        break;
    case '/logout':
        $controller = $container->get('AuthController');
        $controller->logout();
        break;

    // Ticketi
    case '/':
    case '/tickets':
        $controller = $container->get('TicketController');
        $controller->index();
        break;
    case '/tickets/create':
        $controller = $container->get('TicketController');
        $controller->create();
        break;
    case '/tickets/store':
        $controller = $container->get('TicketController');
        $controller->store();
        break;
    case '/ticket/reply':
        $controller = $container->get('TicketController');
        $controller->addReply();
        break;
    case '/ticket/assign':
        $controller = $container->get('TicketController');
        $controller->assign();
        break;

    // Dinamička ruta za pregled pojedinačnog ticketa (/ticket/123)
    default:
        if (preg_match('#^/ticket/(\d+)$#', $uri, $matches)) {
            $controller = $container->get('TicketController');
            $controller->show($matches[1]);
        } else {
            http_response_code(404);
            echo "404 - Stranica nije pronađena";
        }
        break;
}