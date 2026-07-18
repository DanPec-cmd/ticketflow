<?php
// Datoteka: src/Router.php

class Router {
    private $routes = [];

    public function add($method, $uri, $controller, $action) {
        $this->routes[] = compact('method', 'uri', 'controller', 'action');
    }

    public function dispatch($requestUri, $requestMethod) {
        $uri = parse_url($requestUri, PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['uri'] === $uri && $route['method'] === $requestMethod) {
                $controllerName = $route['controller'];
                $action = $route['action'];
                
                $controller = new $controllerName();
                return $controller->$action();
            }
        }
        
        http_response_code(404);
        echo "404 - Stranica nije pronađena";
    }
}