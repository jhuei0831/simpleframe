<?php

use function DI\create;
use Kerwin\Core\Request;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use App\Models\Log\Log;
use App\Models\Twig\LayoutExtension;

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

    'allow_manage' => function (Environment $twig) {
        return new App\Http\Middleware\AllowManageMiddleware($twig);
    },

    'browser' => create(App\Http\Middleware\BrowserMiddleware::class),
];
