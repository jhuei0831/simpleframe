<?php

namespace models\Route\Middleware;

use models\Route\Middleware\Middleware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class AuthMiddleware implements Middleware
{
    private $twig;

    public function __construct(Environment $twig) {
        $this->twig = $twig;
    }

    public function __invoke(Request $request, callable $next)
    {
        if ($request->server->all()['REQUEST_METHOD'] == 'GET') {
            echo $this->twig->render('_error/404.twig');
            return;
        }
        // dump(__CLASS__);

        return $next($request);
    }
}
