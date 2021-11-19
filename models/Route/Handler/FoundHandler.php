<?php

namespace models\Route\Handler;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Response;
use models\Route\Handler\FoundHandlerInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;

class FoundHandler implements FoundHandlerInterface
{
    public function handle(ServerRequestInterface $request, $handler, array $vars): ?ResponseInterface
    {
        $symfonyResponse = new Response('Content');
        $psr17Factory = new Psr17Factory();
        $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        $psrResponse = $psrHttpFactory->createResponse($symfonyResponse);
        return $psrResponse;
    }
}