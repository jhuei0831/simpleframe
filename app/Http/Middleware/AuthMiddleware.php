<?php

namespace App\Http\Middleware;

use Closure;
use Twig\Environment;
use Kerwin\Core\Request;
use Kerwin\Core\Support\Facades\Session;
use Kerwin\Core\Router\Middleware\Middleware;

class AuthMiddleware implements Middleware
{
    private $twig;

    public function __construct(Environment $twig) {
        $this->twig = $twig;
    }

    public function __invoke(Request $request, Closure $next, $arg = NULL)
    {
        if (!Session::get('USER_ID')) {
            echo $this->twig->render('_error/404.twig');
            return;
        }

        return $next($request);
    }
}
