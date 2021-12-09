<?php

namespace App\Http\Middleware;

use Kerwin\Core\Request;
use Kerwin\Core\Router\Middleware\Middleware;
use Jenssegers\Agent\Agent;

class BrowserMiddleware implements Middleware
{

    private $agent;

    public function __construct() {
        $this->agent = new Agent();
    }

    public function __invoke(Request $request, callable $next)
    {
        if ($this->agent->browser() === 'IE') {
            echo '請勿使用IE瀏覽器!';
            return;
        }

        return $next($request);
    }
}
