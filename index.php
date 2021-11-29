<?php
$root = "./";
include($root.'config/settings.php');

use Kerwin\Core\Router\RouteCollector;

$container = require __DIR__ . '/app/bootstrap.php';
$twig = $container->get('twig');
$dispatcher = Kerwin\Core\Router\simpleDispatcher(function (RouteCollector $route) use ($twig) {
    $route->addGroup('/simpleframe', function (RouteCollector $route) use ($twig) {
        $route->addRoute('GET', '/', 'App\Http\Controller\HomeController');
        $route->addRoute('GET', '/captcha', ['App\Http\Controller\Auth\LoginController', 'captcha']);
        $route->addGroup('/auth', function (RouteCollector $route) use ($twig) {
            $route->addRoute('GET', '/login', ['App\Http\Controller\Auth\LoginController', 'index']);
            $route->addRoute('POST', '/login', ['App\Http\Controller\Auth\LoginController', 'login']);
            $route->addRoute('GET', '/logout', ['App\Http\Controller\Auth\LoginController', 'logout']);
            $route->addRoute('GET', '/register', ['App\Http\Controller\Auth\RegisterController', 'index']);
            $route->addRoute('POST', '/register', ['App\Http\Controller\Auth\RegisterController', 'register']);
            $route->middleware([new \App\Http\Middleware\AuthMiddleware($twig)])->addRoute('GET', '/email_verified', ['App\Http\Controller\Auth\EmailVerifiedController', 'index']);
            $route->middleware([new \App\Http\Middleware\AuthMiddleware($twig)])->addRoute('POST', '/email_verified', ['App\Http\Controller\Auth\EmailVerifiedController', 'getVerifyEmail']);
            $route->addRoute('GET', '/check_email_verified/{auth}/{id}', ['App\Http\Controller\Auth\EmailVerifiedController', 'checkVerifyEmail']);
            $route->addRoute('GET', '/password_forgot', ['App\Http\Controller\Auth\PasswordResetController', 'forgot']);
            $route->addRoute('POST', '/password_forgot', ['App\Http\Controller\Auth\PasswordResetController', 'getResetEmail']);
            $route->addRoute('GET', '/password_reset/{auth}/{id}', ['App\Http\Controller\Auth\PasswordResetController', 'index']);
            $route->addRoute('POST', '/password_reset/{auth}/{id}', ['App\Http\Controller\Auth\PasswordResetController', 'reset']);
        });
    });
});

$dispatcher->process($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], $container);
