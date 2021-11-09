<?php

$root = "./";
include($root.'config/settings.php');

use FastRoute\RouteCollector;

$container = require __DIR__ . '/app/bootstrap.php';

$dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $route) {
    $route->addGroup('/simpleframe', function (RouteCollector $route) {
        $route->addRoute('GET', '/', 'Controller\HomeController');
        $route->addRoute('GET', '/captcha', ['Controller\Auth\LoginController', 'captcha']);
        $route->addGroup('/auth', function (RouteCollector $route) {
            $route->addRoute('GET', '/login', ['Controller\Auth\LoginController', 'index']);
            $route->addRoute('POST', '/login', ['Controller\Auth\LoginController', 'login']);
            $route->addRoute('GET', '/logout', ['Controller\Auth\LoginController', 'logout']);
            $route->addRoute('GET', '/register', ['Controller\Auth\RegisterController', 'index']);
            $route->addRoute('POST', '/register', ['Controller\Auth\RegisterController', 'register']);
        });
    });
});

$route = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

switch ($route[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        echo '404 Not Found';
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        echo '405 Method Not Allowed';
        break;

    case FastRoute\Dispatcher::FOUND:
        $controller = $route[1];
        $parameters = $route[2];
        
        // We could do $container->get($controller) but $container->call()
        // does that automatically
        $container->call($controller, $parameters);
        break;
}
