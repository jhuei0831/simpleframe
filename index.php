<?php
$root = "./";
include($root.'config/settings.php');

use Kerwin\Core\Router\RouteCollector;
use function Kerwin\Core\Router\simpleDispatcher;

$container = require __DIR__ . '/app/bootstrap.php';

$dispatcher = simpleDispatcher(function (RouteCollector $route) {
    $route->middleware(['browser'])->addGroup('/simpleframe', function (RouteCollector $route) {
        // 前台
        $route->get('/', 'App\Http\Controller\HomeController');
        $route->get('/captcha', ['App\Http\Controller\Auth\LoginController', 'captcha']);
        // 註冊、登入、忘記密碼、信箱驗證
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
        // 後台
        $route->middleware(['auth', 'permission:manage-index'])->addGroup('/manage', function (RouteCollector $route) {
            $route->get('/', ['App\Http\Controller\Manage\ManageController', 'index']);
            // 權限
            $route->middleware(['permission:permissions-list'])->get('/permissions', ['App\Http\Controller\Manage\PermissionController', 'index']);
            $route->middleware(['permission:permissions-list'])->post('/permissions/datatable', ['App\Http\Controller\Manage\PermissionController', 'dataTable']);
            $route->middleware(['permission:permissions-create'])->get('/permissions/create', ['App\Http\Controller\Manage\PermissionController', 'create']);
            $route->middleware(['permission:permissions-create'])->post('/permissions/create', ['App\Http\Controller\Manage\PermissionController', 'store']);
            $route->middleware(['permission:permissions-edit'])->get('/permissions/edit/{id}', ['App\Http\Controller\Manage\PermissionController', 'edit']);
            $route->middleware(['permission:permissions-edit'])->post('/permissions/edit/{id}', ['App\Http\Controller\Manage\PermissionController', 'update']);
            $route->middleware(['permission:permissions-delete'])->get('/permissions/delete/{id}', ['App\Http\Controller\Manage\PermissionController', 'delete']);
            // 角色
            $route->middleware(['permission:roles-list'])->get('/roles', ['App\Http\Controller\Manage\RoleController', 'index']);
            $route->middleware(['permission:roles-list'])->post('/roles/datatable', ['App\Http\Controller\Manage\RoleController', 'datatable']);
            $route->middleware(['permission:roles-create'])->get('/roles/create', ['App\Http\Controller\Manage\RoleController', 'create']);
            $route->middleware(['permission:roles-create'])->post('/roles/create', ['App\Http\Controller\Manage\RoleController', 'store']);
            $route->middleware(['permission:roles-edit'])->get('/roles/edit/{id}', ['App\Http\Controller\Manage\RoleController', 'edit']);
            $route->middleware(['permission:roles-edit'])->post('/roles/edit/{id}', ['App\Http\Controller\Manage\RoleController', 'update']);
            $route->middleware(['permission:roles-delete'])->get('/roles/delete/{id}', ['App\Http\Controller\Manage\RoleController', 'delete']);
            // 使用者
            $route->middleware(['permission:users-list'])->get('/users', ['App\Http\Controller\Manage\UserController', 'index']);
            $route->middleware(['permission:users-list'])->post('/users/datatable', ['App\Http\Controller\Manage\UserController', 'datatable']);
            $route->middleware(['permission:users-create'])->get('/users/create', ['App\Http\Controller\Manage\UserController', 'create']);
            $route->middleware(['permission:users-create'])->post('/users/create', ['App\Http\Controller\Manage\UserController', 'store']);
            $route->middleware(['permission:users-edit'])->get('/users/edit/{id}', ['App\Http\Controller\Manage\UserController', 'edit']);
            $route->middleware(['permission:users-edit'])->post('/users/edit/{id}', ['App\Http\Controller\Manage\UserController', 'update']);
            $route->middleware(['permission:users-delete'])->get('/users/delete/{id}', ['App\Http\Controller\Manage\UserController', 'delete']);
            // Log
            $route->middleware(['permission:logs-list'])->get('/logs', ['App\Http\Controller\Manage\LogController', 'index']);
            $route->middleware(['permission:logs-list'])->post('/logs/datatable', ['App\Http\Controller\Manage\LogController', 'datatable']);
            // 網站設定
            $route->middleware(['permission:config-edit'])->get('/config', ['App\Http\Controller\Manage\ConfigController', 'index']);
            $route->middleware(['permission:config-edit'])->post('/config/edit/{id}', ['App\Http\Controller\Manage\ConfigController', 'edit']);
        });
    });
});

$dispatcher->process($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], $container);
