<?php

namespace app\core;

class Router
{
    protected $routes = [];
    protected $namedRoutes = [];
    protected $middlewares = [];
    protected $prefix = '';
    protected $currentGroup = [];

    public function get(string $uri, callable|array $action)
    {
        return $this->registerRoute('GET', $uri, $action);
    }

    public function post(string $uri, callable|array $action)
    {
        return $this->registerRoute('POST', $uri, $action);
    }

    public function put(string $uri, callable|array $action)
    {
        return $this->registerRoute('PUT', $uri, $action);
    }

    public function delete(string $uri, callable|array $action)
    {
        return $this->registerRoute('DELETE', $uri, $action);
    }

    public function group(array $options, callable $callback): void
    {
        $previousGroup = $this->currentGroup;
        $previousMiddlewares = $this->middlewares;

        $this->currentGroup = $options;
        if (isset($options['prefix'])) {
            $this->prefix .= '/' . trim($options['prefix'], '/');
        }
        if (isset($options['middleware'])) {
            $middleware = (array)$options['middleware'];
            foreach ($middleware as &$m) {
                if (is_string($m) && isset($GLOBALS[$m])) {
                    $m = $GLOBALS[$m];
                }
            }
            $this->middlewares = array_merge($this->middlewares, $middleware);
        }

        $callback($this);

        $this->currentGroup = $previousGroup;
        $this->middlewares = $previousMiddlewares;
        $this->prefix = rtrim(dirname($this->prefix), '/') ?: '';
    }

    public function registerRoute(string $method, string $uri, callable|array $action)
    {
        $uri = $this->prefix . '/' . trim($uri, '/');
        $route = [
            'method' => $method,
            'uri' => $uri,
            'action' => $action,
            'middleware' => $this->middlewares,
            'name' => null,
        ];

        $this->routes[] = $route;

        return new class($route, $this) {
            private array $route;
            private Router $router;

            public function __construct(array $route, Router $router)
            {
                $this->route = $route;
                $this->router = $router;
            }

            public function name(string $name): Router
            {
                $this->route['name'] = $name;
                $this->router->addNamedRoute($name, $this->route); // Use public method to add the named route
                return $this->router;
            }
        };
    }

    public function addNamedRoute(string $name, array $route): void
    {
        $this->namedRoutes[$name] = $route;
    }

    public function route(string $name, array $parameters = []): string
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new \Exception("Route '{$name}' not found");
        }

        $uri = $this->namedRoutes[$name]['uri'];

        // Replace route parameters
        foreach ($parameters as $key => $value) {
            $uri = preg_replace('#\{' . preg_quote($key) . '\}#', $value, $uri);
        }

        return $uri;
    }

    public function dispatch(string $method, string $uri)
    {
        foreach ($this->routes as $route) {
            if ($this->match($method, $uri, $route)) {
                $this->handleMiddleware($route['middleware']);
                return $this->callAction($route['action'], $this->extractParameters($route['uri'], $uri));
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }

    protected function match(string $method, string $uri, array $route): bool
    {
        return $method === $route['method'] && preg_match($this->convertToRegex($route['uri']), $uri);
    }

    protected function convertToRegex(string $uri): string
    {
        return '#^' . preg_replace('#\{[\w]+\}#', '([\w\-\.]+)', $uri) . '$#';
    }

    protected function extractParameters(string $routeUri, string $uri): array
    {
        $pattern = $this->convertToRegex($routeUri);
        preg_match($pattern, $uri, $matches);
        array_shift($matches); // Remove the full match
        return $matches;
    }

    protected function handleMiddleware(array $middlewares): void
    {
        foreach ($middlewares as $middleware) {
            if (is_callable($middleware)) {
                $middleware();
            } elseif (is_string($middleware) && isset($GLOBALS[$middleware])) {
                $middleware = $GLOBALS[$middleware];
                $middleware();
            }
        }
    }

    protected function callAction(callable|array $action, array $parameters)
    {
        if (is_callable($action)) {
            return call_user_func_array($action, $parameters);
        }

        [$controller, $method] = $action;
        if (class_exists($controller) && method_exists($controller, $method)) {
            $controllerInstance = new $controller();
            return call_user_func_array([$controllerInstance, $method], $parameters);
        }

        throw new \Exception("Controller or method not found");
    }
}
