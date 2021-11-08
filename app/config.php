<?php

use function DI\create;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use models\Twig\LayoutExtension;
// use SuperBlog\Model\ArticleRepository;
// use SuperBlog\Persistence\InMemoryArticleRepository;

return [
    // Bind an interface to an implementation
    // ArticleRepository::class => create(InMemoryArticleRepository::class),

    // Configure Twig
    Environment::class => function () {
        $loader = new FilesystemLoader(__DIR__ . '/../views');
        $twig = new Environment($loader);
        $twig->addExtension(new LayoutExtension());
        return $twig;
    },
];
