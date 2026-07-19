<?php
// Datoteka: src/Router.php

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
            'middlewares' => $middlewares // Ovdje definiraš npr: ['auth', 'csrf']
        ];
    }

    public function dispatch($requestUri, $requestMethod) {
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
                $controller = new $route['controller'](); // Ovdje po potrebi koristi Container
                return call_user_func_array([$controller, $route['action']], $matches);
            }
        }

        // 3. Rukovanje greškama
        if ($methodNotAllowed) {
            http_response_code(405);
            echo "405 - Metoda nije dopuštena";
        } else {
            http_response_code(404);
            echo "404 - Stranica nije pronađena";
        }
    }

    private function runMiddleware($name) {
        if ($name === 'auth') {
            AuthGuard::requireLogin();
        } elseif ($name === 'csrf') {
            // Centralizirana CSRF provjera
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                    die("CSRF token neispravan.");
                }
            }
        }
    }
}