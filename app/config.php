<?php

use function DI\create;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use models\Log\Log;
use models\Twig\LayoutExtension;
use Symfony\Component\HttpFoundation\Request;
// use SuperBlog\Model\ArticleRepository;
// use SuperBlog\Persistence\InMemoryArticleRepository;

return [
    // Bind an interface to an implementation
    // LoggerInterface::class => create(InMemoryArticleRepository::class),

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
        $twig->getExtension(\Twig\Extension\EscaperExtension::class)->setEscaper('html_no_quotes', function($string) {
            return htmlspecialchars($string, ENT_NOQUOTES);
        });
        return $twig;
    },
];
