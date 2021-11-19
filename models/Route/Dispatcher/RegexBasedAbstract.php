<?php

namespace models\Route\Dispatcher;

use Twig\Environment;
use FastRoute\Dispatcher;
use models\Route\MiddlewareStack;
use models\Route\MiddlewareRunner;

abstract class RegexBasedAbstract implements Dispatcher
{
    /** @var mixed[][] */
    protected $staticRouteMap = [];

    /** @var mixed[] */
    protected $variableRouteData = [];

    /**
     * @return mixed[]
     */
    abstract protected function dispatchVariableRoute($routeData, $uri);

    public function process($httpMethod, $uri, $container)
    {
        $route = $this->dispatch($httpMethod, $uri);
        switch ($route[0]) {
            case self::NOT_FOUND:
                $container->call(function(Environment $twig) {
                    echo $twig->render('_error/404.twig');
                });
                break;
                
            case self::METHOD_NOT_ALLOWED:
                $container->call(function(Environment $twig) {
                    echo $twig->render('_error/404.twig');
                });
                break;

            case self::FOUND:
                $controller = $route[1];
                $parameters = $route[2];
                $response = function() use ($container, $controller, $parameters) {
                    $container->call($controller, $parameters);
                };
                $middleware = new MiddlewareRunner(new MiddlewareStack($response));
                if (is_array($route[3])) {
                    foreach ($route[3] as $value) {
                        $middleware->add($value);
                    }
                }
                $middleware->run();
                // We could do $container->get($controller) but $container->call()
                // does that automatically
                // $container->call($controller, $parameters);
                break;
        }
    }

    public function dispatch($httpMethod, $uri)
    {
        if (isset($this->staticRouteMap[$httpMethod][$uri])) {
            $handler = $this->staticRouteMap[$httpMethod][$uri];
            $middleware = $this->staticRouteMap[$httpMethod]['middleware'][$uri];
            return [self::FOUND, $handler, [], $middleware];
        }

        $varRouteData = $this->variableRouteData;
        if (isset($varRouteData[$httpMethod])) {
            $result = $this->dispatchVariableRoute($varRouteData[$httpMethod], $uri);
            if ($result[0] === self::FOUND) {
                return $result;
            }
        }

        // For HEAD requests, attempt fallback to GET
        if ($httpMethod === 'HEAD') {
            if (isset($this->staticRouteMap['GET'][$uri])) {
                $handler = $this->staticRouteMap['GET'][$uri];
                return [self::FOUND, $handler, []];
            }
            if (isset($varRouteData['GET'])) {
                $result = $this->dispatchVariableRoute($varRouteData['GET'], $uri);
                if ($result[0] === self::FOUND) {
                    return $result;
                }
            }
        }

        // If nothing else matches, try fallback routes
        if (isset($this->staticRouteMap['*'][$uri])) {
            $handler = $this->staticRouteMap['*'][$uri];
            return [self::FOUND, $handler, []];
        }
        if (isset($varRouteData['*'])) {
            $result = $this->dispatchVariableRoute($varRouteData['*'], $uri);
            if ($result[0] === self::FOUND) {
                return $result;
            }
        }

        // Find allowed methods for this URI by matching against all other HTTP methods as well
        $allowedMethods = [];

        foreach ($this->staticRouteMap as $method => $uriMap) {
            if ($method !== $httpMethod && isset($uriMap[$uri])) {
                $allowedMethods[] = $method;
            }
        }

        foreach ($varRouteData as $method => $routeData) {
            if ($method === $httpMethod) {
                continue;
            }

            $result = $this->dispatchVariableRoute($routeData, $uri);
            if ($result[0] === self::FOUND) {
                $allowedMethods[] = $method;
            }
        }

        // If there are no allowed methods the route simply does not exist
        if ($allowedMethods) {
            return [self::METHOD_NOT_ALLOWED, $allowedMethods];
        }

        return [self::NOT_FOUND];
    }
}