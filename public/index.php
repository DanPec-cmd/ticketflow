<?php
// Datoteka: public/index.php (ili root index.php ovisno o strukturi)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// ==========================================
// 1. POSTAVLJANJE SIGURNOSNIH HEADERA
// ==========================================
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com; style-src 'self' 'unsafe-inline';");


// ==========================================
// 2. UČITAVANJE .ENV DATOTEKE
// ==========================================
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $_ENV[trim($name)] = trim($value);
        }
    }
}


// ==========================================
// 3. INICIJALIZACIJA ERROR REPORTINGA
// ==========================================
$appEnv = $_ENV['APP_ENV'] ?? 'production';

if ($appEnv === 'development') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
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
require_once __DIR__ . '/../src/Router.php'; // NOVO: Uključivanje rutera

$container = new Container();

// 1. Instanciraj bazu samo jednom!
$dbConnection = (new Database())->getConnection();

// 2. Registracija PDO konekcije koja će se uvijek vraćati
$container->bind('Database', function($c) use ($dbConnection) {
    return $dbConnection;
});

// 3. Modeli sada primaju bazu kroz konstruktor
$container->bind('User', function($c) {
    return new User($c->get('Database'));
});
$container->bind('Ticket', function($c) {
    return new Ticket($c->get('Database'));
});

// 4. Kontroleri ostaju isti, primaju modele iz kontejnera
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
    // Inicijaliziramo ruter (u Router.php bi trebao prilagoditi da koristi Container za dohvat klasa, 
    // ili možeš ovdje proslijediti $container ako prilagodiš __construct rutera)
    $router = new Router();
    
    /**
     * DEFINICIJA RUTA
     * Sintaksa: $router->add(Metoda, Putanja, Kontroler, Akcija, [Middlewares]);
     */
    
    // Javne rute (Gosti)
    $router->add('GET',  '/login',           'AuthController', 'showLoginForm');
    $router->add('POST', '/login/submit',    'AuthController', 'login',            ['csrf']); // Dodan CSRF za prijavu
    $router->add('GET',  '/register',        'AuthController', 'showRegisterForm');
    $router->add('POST', '/register/submit', 'AuthController', 'register',         ['csrf']); // Dodan CSRF za registraciju
    $router->add('GET',  '/logout',          'AuthController', 'logout');

    // Zaštićene rute (Prijavljeni korisnici)
    $router->add('GET',  '/',                'TicketController', 'index',  ['auth']);
    $router->add('GET',  '/tickets',         'TicketController', 'index',  ['auth']);
    $router->add('GET',  '/tickets/create',  'TicketController', 'create', ['auth']);
    $router->add('POST', '/tickets/store',   'TicketController', 'store',  ['auth', 'csrf']); // Forma = CSRF zaštita
    
    // Akcije na ticketima
    $router->add('POST', '/ticket/reply',    'TicketController', 'addReply', ['auth', 'csrf']);
    $router->add('POST', '/ticket/assign',   'TicketController', 'assign',   ['auth', 'csrf']);
    
    // Dinamička ruta za prikaz ticketa
    $router->add('GET',  '/ticket/{id}',     'TicketController', 'show',   ['auth']);

    // Dohvaćamo trenutni URI (čistimo trailing slash)
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if ($uri !== '/' && substr($uri, -1) === '/') {
        $uri = rtrim($uri, '/');
    }

    // DISPATCH - Ruter sada sam rješava pokretanje kontrolera i bacanje 404/405 grešaka
    $router->dispatch($uri, $_SERVER['REQUEST_METHOD'], $container);

} catch (Throwable $e) {
    // 1. Logiraj pravu grešku na serveru
    error_log("Uncaught Exception/Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());

    // 2. Vrati 500 status kod
    http_response_code(500);

    // 3. Prikaz greške ovisno o okruženju
    if ($appEnv !== 'development') {
        echo "<h1>500 Internal Server Error (Dev Mode)</h1>";
        echo "<strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
        echo "<strong>File:</strong> " . htmlspecialchars($e->getFile()) . " (Line: " . $e->getLine() . ")<br>";
        echo "<h3>Stack Trace:</h3>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    } else {
        echo "<!DOCTYPE html>
              <html>
              <head><title>500 - Interna greška</title></head>
              <body style='text-align: center; padding: 50px; font-family: sans-serif;'>
                <h1>500</h1>
                <h3>Došlo je do interne greške na serveru.</h3>
                <p>Naš tim je obaviješten. Molimo pokušajte ponovno senare.</p>
              </body>
              </html>";
    }
}