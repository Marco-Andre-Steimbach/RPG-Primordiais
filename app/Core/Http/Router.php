<?php

namespace App\Core\Http;

use App\Application\Middlewares\AuthMiddleware;
use App\Application\Middlewares\RoleMiddleware;
use App\Core\Exceptions\NotFoundException;

class Router
{
    private array $routes = [];
    private array $routeMiddlewares = [];
    private array $currentMiddlewares = [];

    public function middleware(string $name): self
    {
        $this->currentMiddlewares[] = $name;
        return $this;
    }

    public function add(string $method, string $path, callable|array $action): void
    {
        $method = strtoupper($method);
        $this->routes[$method][$path] = $action;

        if (!empty($this->currentMiddlewares)) {
            $this->routeMiddlewares[$method][$path] = $this->currentMiddlewares;
            $this->currentMiddlewares = [];
        }
    }

    public function dispatch(Request $request)
    {
        $method = $request->method();
        $uri = $request->uri();

        if (!isset($this->routes[$method][$uri])) {
            throw new NotFoundException('Rota não encontrada');
        }

        if (isset($this->routeMiddlewares[$method][$uri])) {

            $middlewareMap = [
                'auth' => AuthMiddleware::class,
                'role' => RoleMiddleware::class,
            ];

            foreach ($this->routeMiddlewares[$method][$uri] as $middleware) {

                [$name, $params] = array_pad(explode(':', $middleware, 2), 2, null);

                if (!isset($middlewareMap[$name])) {
                    continue;
                }

                $instance = new $middlewareMap[$name]();

                if ($params !== null) {
                    $instance->handle($request, explode(',', $params));
                } else {
                    $instance->handle($request);
                }
            }
        }

        $action = $this->routes[$method][$uri];

        if (is_array($action)) {
            [$controller, $methodName] = $action;
            $controller = new $controller();
            return $controller->$methodName($request);
        }

        return $action($request);
    }
}
