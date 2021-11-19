<?php
$root = "./";
include($root.'config/settings.php');

use FastRoute\RouteCollector;

$container = require __DIR__ . '/app/bootstrap.php';
$twig = $container->get('twig');
$dispatcher = models\Route\simpleDispatcher(function (RouteCollector $route) use ($twig) {
    $route->addGroup('/jhuei0831/simpleframe', function (RouteCollector $route) use ($twig) {
        $route->addRoute('GET', '/', 'Controller\HomeController');
        $route->addRoute('GET', '/captcha', ['Controller\Auth\LoginController', 'captcha']);
        $route->addGroup('/auth', function (RouteCollector $route) use ($twig) {
            $route->middleware(new \models\Route\Middleware\AuthMiddleware($twig))->addRoute('GET', '/login', ['Controller\Auth\LoginController', 'index']);
            $route->addRoute('POST', '/login', ['Controller\Auth\LoginController', 'login']);
            $route->addRoute('GET', '/logout', ['Controller\Auth\LoginController', 'logout']);
            $route->addRoute('GET', '/register', ['Controller\Auth\RegisterController', 'index']);
            $route->addRoute('POST', '/register', ['Controller\Auth\RegisterController', 'register']);
            $route->addRoute('GET', '/email_verified', ['Controller\Auth\EmailVerifiedController', 'index']);
            $route->addRoute('POST', '/email_verified', ['Controller\Auth\EmailVerifiedController', 'getVerifyEmail']);
            $route->addRoute('GET', '/check_email_verified/{auth}/{id}', ['Controller\Auth\EmailVerifiedController', 'checkVerifyEmail']);
            $route->addRoute('GET', '/password_forgot', ['Controller\Auth\PasswordResetController', 'forgot']);
            $route->addRoute('POST', '/password_forgot', ['Controller\Auth\PasswordResetController', 'getResetEmail']);
            $route->addRoute('GET', '/password_reset/{auth}/{id}', ['Controller\Auth\PasswordResetController', 'index']);
            $route->addRoute('POST', '/password_reset/{auth}/{id}', ['Controller\Auth\PasswordResetController', 'reset']);
        });
    });
});

$dispatcher->process($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], $container);
