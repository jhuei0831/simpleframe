<?php
    $root = '../../';
    include_once($root.'config/settings.php');

    use models\Auth\Email;

    if (isset($_GET['auth']) && isset($_GET['id'])) {
        $email = new Email();
        $email->checkVerifyEmail($_GET);
    }
    else{
        include_once($root.'_error/404.php');
        exit;
    }
?>