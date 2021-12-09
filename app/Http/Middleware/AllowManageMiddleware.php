<?php

namespace App\Http\Middleware;

use Twig\Environment;
use Kerwin\Core\Request;
use Kerwin\Core\Support\Facades\Permission;
use Kerwin\Core\Router\Middleware\Middleware;

class AllowManageMiddleware implements Middleware
{

    private $twig;

    public function __construct(Environment $twig) {
        $this->twig = $twig;
    }

    public function __invoke(Request $request, callable $next)
    {
        if (!Permission::can('manage-index')) {
            echo $this->twig->render('_error/404.twig');
            return;
        }

        return $next($request);
    }
}
