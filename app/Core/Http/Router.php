<?php

namespace App\Core\Http;

class Router
{
    private array $routes = [];

    public function add(string $method, string $path, callable|array $action): void
    {
        $this->routes[strtoupper($method)][$path] = $action;
    }

    public function dispatch(Request $request)
    {
        $method = $request->method();
        $uri = $request->uri();

        if (!isset($this->routes[$method][$uri])) {
            return Response::json(["error" => "Rota não encontrada"], 404);
        }

        $action = $this->routes[$method][$uri];

        if (is_array($action)) {
            [$controller, $methodName] = $action;
            $controller = new $controller;
            return $controller->$methodName($request);
        }

        return $action($request);
    }
}
