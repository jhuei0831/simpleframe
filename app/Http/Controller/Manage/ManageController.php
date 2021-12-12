<?php

namespace App\Http\Controller\Manage;

use Twig\Environment;

class ManageController
{
        
    /**
     * 後台首頁
     *
     * @param  \Twig\Environment $twig
     * @return void
     */
    public function index(Environment $twig)
    {
        echo $twig->render('manage/index.twig');
    }
}
