<?php
// core/Router.php

class Router
{
    private $routes = [];

    public function get($uri, $callback) {
        $this->routes['GET'][$uri] = $callback;
    }

    public function post($uri, $callback) {
        $this->routes['POST'][$uri] = $callback;
    }

    public function dispatch($method, $uri) {
        $uri = parse_url($uri, PHP_URL_PATH);
        $callback = $this->routes[$method][$uri] ?? null;

        if (!$callback) {
            http_response_code(404);
            include __DIR__ . '/../views/error/404.php';
            exit;
        }

        // 🟢 Fix here: instantiate controller and call method
        if (is_array($callback)) {
            $controller = new $callback[0]();
            $method = $callback[1];
            call_user_func([$controller, $method]);
        } else {
            call_user_func($callback);
        }
    }
}