<?php

namespace App\Http\Middleware;

use Closure;
use Kerwin\Core\Request;
use Jenssegers\Agent\Agent;
use Kerwin\Core\Router\Middleware\Middleware;

class BrowserMiddleware implements Middleware
{

    private $agent;

    public function __construct() {
        $this->agent = new Agent();
    }

    public function __invoke(Request $request, Closure $next, $arg = NULL)
    {
        if ($this->agent->browser() === 'IE') {
            echo '請勿使用IE瀏覽器!';
            return;
        }

        return $next($request);
    }
}
