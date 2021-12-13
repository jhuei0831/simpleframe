<?php

use App\Models\User;
use Twig\Environment;
use function DI\create;
use Kerwin\Core\Request;
use App\Services\Log\Log;
use Twig\Loader\FilesystemLoader;
use App\Services\Twig\LayoutExtension;

return [

    Log::class => function() {
        $log = new Log();
        return $log;
    },

    Request::class => function() {
        $request = Request::createFromGlobals();
        return $request;
    },

    // Configure Twig
    Environment::class => function () {
        $loader = new FilesystemLoader(__DIR__ . '/../views');
        $twig = new Environment($loader);
        $twig->addExtension(new LayoutExtension());
        return $twig;
    },

    // Middleware
    'auth' => function (Environment $twig) {
        return new App\Http\Middleware\AuthMiddleware($twig);
    },

    'permission' => function (Environment $twig) {
        return new App\Http\Middleware\PermissionsMiddleware($twig);
    },

    'browser' => create(App\Http\Middleware\BrowserMiddleware::class),

    // Models
    User::class => create(User::class)
];
