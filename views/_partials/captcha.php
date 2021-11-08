<?php
    
    $root = "../";
    include($root.'config/settings.php');

    //設置定義為圖片
    header("Content-type: image/PNG");

    use Kerwin\Captcha\Captcha;
    $captcha = new Captcha();
    $captcha->getImageCode(1,5,130,30);