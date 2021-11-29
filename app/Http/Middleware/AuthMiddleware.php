<?php

namespace App\Http\Middleware;

use Kerwin\Core\Support\Facades\Session;
use Kerwin\Core\Router\Middleware\Middleware;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

class AuthMiddleware implements Middleware
{
    private $twig;

    public function __construct(Environment $twig) {
        $this->twig = $twig;
    }

    public function __invoke(Request $request, callable $next)
    {
        if (!Session::get('USER_ID')) {
            echo $this->twig->render('_error/404.twig');
            return;
        }

        return $next($request);
    }
}
