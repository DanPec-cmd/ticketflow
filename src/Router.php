<?php
// Datoteka: src/Router.php

class Router {
    private $routes = [];

    public function add($method, $path, $controller, $action) {
        $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', $path);
        $regex = '#^' . $pattern . '/?$#';

        $this->routes[] = [
            'method'     => $method,
            'regex'      => $regex,
            'controller' => $controller,
            'action'     => $action
        ];
    }

    public function dispatch($requestUri, $requestMethod) {
        $uri = parse_url($requestUri, PHP_URL_PATH);

        foreach ($this->routes as $route) {
       
            if ($route['method'] === $requestMethod && preg_match($route['regex'], $uri, $matches)) {
                
             
                array_shift($matches);

                $controllerName = $route['controller'];
                $actionName = $route['action'];

                if (class_exists($controllerName)) {
                    $controller = new $controllerName();
                    
                    if (method_exists($controller, $actionName)) {
     
                        return call_user_func_array([$controller, $actionName], $matches);
                    }
                }
            }
        }
        
        http_response_code(404);
        echo "404 - Stranica nije pronađena";
    }
}