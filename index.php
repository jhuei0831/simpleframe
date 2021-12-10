<?php
$root = "./";
include($root.'config/settings.php');

use Kerwin\Core\Router\RouteCollector;
use function Kerwin\Core\Router\simpleDispatcher;

$container = require __DIR__ . '/app/bootstrap.php';

$dispatcher = simpleDispatcher(function (RouteCollector $route) {
    $route->middleware(['browser'])->addGroup('/simpleframe', function (RouteCollector $route) {
        $route->get('/', 'App\Http\Controller\HomeController');
        $route->get('/captcha', ['App\Http\Controller\Auth\LoginController', 'captcha']);
        $route->addGroup('/auth', function (RouteCollector $route) {
            $route->get('/login', ['App\Http\Controller\Auth\LoginController', 'index']);
            $route->post('/login', ['App\Http\Controller\Auth\LoginController', 'login']);
            $route->get('/logout', ['App\Http\Controller\Auth\LoginController', 'logout']);
            $route->get('/register', ['App\Http\Controller\Auth\RegisterController', 'index']);
            $route->post('/register', ['App\Http\Controller\Auth\RegisterController', 'register']);
            $route->middleware(['auth'])->get('/email_verified', ['App\Http\Controller\Auth\EmailVerifiedController', 'index']);
            $route->middleware(['auth'])->post('/email_verified', ['App\Http\Controller\Auth\EmailVerifiedController', 'getVerifyEmail']);
            $route->get('/check_email_verified/{auth}/{id}', ['App\Http\Controller\Auth\EmailVerifiedController', 'checkVerifyEmail']);
            $route->get('/password_forgot', ['App\Http\Controller\Auth\PasswordResetController', 'forgot']);
            $route->post('/password_forgot', ['App\Http\Controller\Auth\PasswordResetController', 'getResetEmail']);
            $route->get('/password_reset/{auth}/{id}', ['App\Http\Controller\Auth\PasswordResetController', 'index']);
            $route->post('/password_reset/{auth}/{id}', ['App\Http\Controller\Auth\PasswordResetController', 'reset']);
        });
        $route->middleware(['auth', 'allow_manage:permssions-list'])->addGroup('/manage', function (RouteCollector $route) {
            $route->get('/', ['App\Http\Controller\Manage\ManageController', 'index']);
            $route->get('/permissions', ['App\Http\Controller\Manage\PermissionsController', 'index']);
            $route->post('/permissions/datatable', ['App\Http\Controller\Manage\PermissionsController', 'dataTable']);
        });
    });
});

$dispatcher->process($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], $container);
