<?php

namespace App\Http\Controller\Manage;

use Twig\Environment;

class ManageController
{
    
    public function index(Environment $twig)
    {
        echo $twig->render('manage/index.twig');
    }
}
