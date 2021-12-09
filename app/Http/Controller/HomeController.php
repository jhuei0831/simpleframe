<?php

namespace App\Http\Controller;

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

    public function __invoke()
    {
        $auth = Auth::user();
        
        echo $this->twig->render('home.twig', [
            'auth' => $auth,
        ]);
    }
}
