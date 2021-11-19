<?php

namespace models\Route;

use models\Route\MiddlewareStack;
use models\Route\Middleware\Middleware;

class MiddlewareRunner
{
    protected $middleware;

	public function __construct(MiddlewareStack $middleware) {
		$this->middleware = $middleware;
	}

	public function add(Middleware $middleware)
	{
		$this->middleware->add($middleware);
	}

	public function run()
	{
		$this->middleware->handle();

		// dump('run app');
	}
}