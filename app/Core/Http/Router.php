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

        if (!isset($this->routes[$method])) {
            throw new NotFoundException('Rota não encontrada');
        }

        foreach ($this->routes[$method] as $route => $action) {

            $pattern = preg_replace('#:([\w]+)#', '(?P<$1>[^/]+)', $route);
            $pattern = "#^{$pattern}$#";

            if (preg_match($pattern, $uri, $matches)) {

                $params = [];
                foreach ($matches as $key => $value) {
                    if (!is_int($key)) {
                        $params[$key] = $value;
                    }
                }

                $request->setParams($params);

                if (isset($this->routeMiddlewares[$method][$route])) {

                    $middlewareMap = [
                        'auth' => AuthMiddleware::class,
                        'role' => RoleMiddleware::class,
                    ];

                    foreach ($this->routeMiddlewares[$method][$route] as $middleware) {

                        [$name, $paramsMw] = array_pad(explode(':', $middleware, 2), 2, null);

                        if (!isset($middlewareMap[$name])) {
                            continue;
                        }

                        $instance = new $middlewareMap[$name]();

                        if ($paramsMw !== null) {
                            $instance->handle($request, explode(',', $paramsMw));
                        } else {
                            $instance->handle($request);
                        }
                    }
                }

                if (is_array($action)) {
                    [$controller, $methodName] = $action;
                    $controller = new $controller();
                    return $controller->$methodName($request);
                }

                return $action($request);
            }
        }

        throw new NotFoundException('Rota não encontrada');
    }

}
