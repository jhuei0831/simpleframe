<?php
    $root = '../../';
    include_once($root.'_config/settings.php');

    use _models\Auth\Email;

    if (isset($_GET['auth']) && isset($_GET['id'])) {
        $email = new Email();
        $email->checkVerifyEmail($_GET);
    }
    else{
        include_once($root.'_error/404.php');
        exit;
    }
?>