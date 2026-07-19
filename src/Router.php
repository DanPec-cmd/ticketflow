<?php
// Datoteka: src/Router.php
namespace App;

use App\Core\AuthGuard;

class Router {
    private $routes = [];

    public function add($method, $path, $controller, $action, $middlewares = []) {
        $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', $path);
        $regex = '#^' . $pattern . '/?$#';

        $this->routes[] = [
            'method'      => $method,
            'regex'       => $regex,
            'controller'  => $controller,
            'action'      => $action,
            'middlewares' => $middlewares
        ];
    }

    public function dispatch($requestUri, $requestMethod, $container = null) {
        $uri = parse_url($requestUri, PHP_URL_PATH);
        $methodNotAllowed = false;

        foreach ($this->routes as $route) {
            // Provjera rute i metode
            if (preg_match($route['regex'], $uri, $matches)) {
                if ($route['method'] !== $requestMethod) {
                    $methodNotAllowed = true;
                    continue; // Provjeri ostale rute za ispravnu metodu
                }

                // 1. Izvrši Middlewares (Auth, CSRF, itd.)
                foreach ($route['middlewares'] as $middleware) {
                    $this->runMiddleware($middleware);
                }

                // 2. Izvrši kontroler
                array_shift($matches);
                
                // Dohvaćanje kontrolera iz Containera ako postoji, inače standardni 'new'
                $controllerName = $route['controller'];
                $controller = $container ? $container->get($controllerName) : new $controllerName(); 
                
                return call_user_func_array([$controller, $route['action']], $matches);
            }
        }

        // 3. Rukovanje greškama s profesionalnim UI-jem
        if ($methodNotAllowed) {
            $this->renderError(405, "Metoda nije dopuštena", "Pokušavate pristupiti ruti s pogrešnom HTTP metodom.");
        } else {
            $this->renderError(404, "Stranica nije pronađena", "Tražena stranica ne postoji ili je premještena.");
        }
    }

    private function runMiddleware($name) {
        if ($name === 'auth') {
            AuthGuard::requireLogin();
        } elseif ($name === 'csrf') {
            // Centralizirana CSRF provjera
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $sessionToken = $_SESSION['csrf_token'] ?? '';
                $postToken = $_POST['csrf_token'] ?? '';

                if (empty($sessionToken) || empty($postToken) || !hash_equals($sessionToken, $postToken)) {
                    // Prekidamo izvršavanje i bacamo lijepu 403 grešku
                    $this->renderError(403, "Zabranjen pristup", "Vaša sesija je istekla ili je sigurnosni token neispravan. Molimo osvježite stranicu i pokušajte ponovno.");
                }
            }
        }
    }

    // Pomoćna metoda za generiranje Tailwind CSS ekrana za greške
    private function renderError($code, $title, $message) {
        http_response_code($code);
        echo "<!DOCTYPE html>
        <html lang='hr'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>{$code} - {$title}</title>
            <script src='https://cdn.tailwindcss.com'></script>
        </head>
        <body class='bg-slate-100 h-screen flex items-center justify-center p-4'>
            <div class='bg-white p-8 rounded-xl shadow-lg max-w-md w-full text-center border-t-4 border-red-500'>
                <h1 class='text-7xl font-extrabold text-red-500 mb-4'>{$code}</h1>
                <h2 class='text-2xl font-bold text-slate-800 mb-3'>{$title}</h2>
                <p class='text-slate-600 mb-8'>{$message}</p>
                <a href='/' class='inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200'>
                    Povratak na naslovnicu
                </a>
            </div>
        </body>
        </html>";
        exit; // Važno: zaustavlja izvršavanje skripte nakon prikaza greške!
    }
}