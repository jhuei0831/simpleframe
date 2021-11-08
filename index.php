<?php

$root = "./";
include($root.'config/settings.php');

use FastRoute\RouteCollector;

$container = require __DIR__ . '/app/bootstrap.php';

$dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $r) {
    $r->addGroup('/simpleframe', function (RouteCollector $r) {
        $r->addRoute('GET', '/', 'Controller\HomeController');
        $r->addRoute('GET', '/captcha', ['Controller\Auth\LoginController', 'captcha']);
        $r->addGroup('/auth', function (RouteCollector $r) {
            $r->addRoute('GET', '/login', ['Controller\Auth\LoginController', 'index']);
            $r->addRoute('POST', '/login', ['Controller\Auth\LoginController', 'login']);
            $r->addRoute('GET', '/logout', ['Controller\Auth\LoginController', 'logout']);
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
