<?php
// Datoteka: public/index.php (ili root index.php ovisno o strukturi)

session_start();

// ==========================================
// 1. POSTAVLJANJE SIGURNOSNIH HEADERA
// ==========================================
// Sprečavanje Clickjacking napada (dozvoljava iframe samo na istoj domeni)
header("X-Frame-Options: SAMEORIGIN");
// Sprečavanje MIME-type sniffing napada
header("X-Content-Type-Options: nosniff");
// Osnovni Content Security Policy (dopušta učitavanje skripti sa same domene i Tailwind CDN-a koji koristiš u Views)
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com; style-src 'self' 'unsafe-inline';");


// ==========================================
// 2. UČITAVANJE .ENV DATOTEKE
// ==========================================
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


// ==========================================
// 3. INICIJALIZACIJA ERROR REPORTINGA
// ==========================================
// Preuzimamo APP_ENV iz .env (ako ne postoji, pretpostavljamo da je 'production' radi sigurnosti)
$appEnv = $_ENV['APP_ENV'] ?? 'production';

if ($appEnv === 'development') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    // U produkciji gasimo prikazivanje grešaka, ali ih ostavljamo upaljene za sistemske logove
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(E_ALL); 
}


// ==========================================
// 4. UKLJUČIVANJE KLASA I CONTAINER
// ==========================================
require_once __DIR__ . '/../src/Core/Container.php';
require_once __DIR__ . '/../src/Core/AuthGuard.php';
require_once __DIR__ . '/../src/Models/Database.php';
require_once __DIR__ . '/../src/Models/User.php';
require_once __DIR__ . '/../src/Models/Ticket.php';
require_once __DIR__ . '/../src/Controllers/AuthController.php';
require_once __DIR__ . '/../src/Controllers/TicketController.php';

// Inicijalizacija Service Containera
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


// ==========================================
// 5. RUTIRANJE S CENTRALIZIRANIM TRY/CATCH-om
// ==========================================
try {
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
} catch (Throwable $e) {
    // Throwable hvata sve iznimke (Exceptions) i fatalne greške (Errors) u PHP 7+

    // 1. Logiraj pravu grešku na serveru (nikad je ne pokazuj krajnjem korisniku u produkciji)
    error_log("Uncaught Exception/Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());

    // 2. Vrati 500 status kod
    http_response_code(500);

    // 3. Prikaz (ovisno o okruženju)
    if ($appEnv === 'development') {
        // U dev okruženju ispisujemo grešku da nam olakša debugiranje
        echo "<h1>500 Internal Server Error (Dev Mode)</h1>";
        echo "<strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
        echo "<strong>File:</strong> " . htmlspecialchars($e->getFile()) . " (Line: " . $e->getLine() . ")<br>";
        echo "<h3>Stack Trace:</h3>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    } else {
        // U produkciji samo generička poruka bez curenja sistemskih putanja i upita
        echo "<!DOCTYPE html>
              <html>
              <head><title>500 - Interna greška</title></head>
              <body style='text-align: center; padding: 50px; font-family: sans-serif;'>
                <h1>500</h1>
                <h3>Došlo je do interne greške na serveru.</h3>
                <p>Naš tim je obaviješten. Molimo pokušajte ponovno kasnije.</p>
              </body>
              </html>";
    }
}