<?php

namespace Controller;

use Twig\Environment;
use Kerwin\Core\Support\Facades\Auth;

class HomeController
{

    /**
     * @var Twig_Environment
     */
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Example of an invokable class, i.e. a class that has an __invoke() method.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.invoke
     */
    public function __invoke()
    {
        $auth = Auth::user();
        
        echo $this->twig->render('home.twig', [
            'auth' => $auth,
        ]);
    }
}
