<?php

namespace models\Route\Middleware;

use models\Route\Middleware\Middleware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class BrowserMiddleware implements Middleware
{
    private $twig;

    public function __construct(Environment $twig) {
        $this->twig = $twig;
    }

    public function __invoke(Request $request, callable $next)
    {
        if ($request->server->all()['REQUEST_METHOD'] == 'GET') {
            $view =  $this->twig->render('_error/404.twig');
            $response = new Response($view, 404);
            $response->prepare($request);
            return $response->send();
        }
        // dump(__CLASS__);

        return $next($request);
    }
}
