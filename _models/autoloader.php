<?php
    function autoloader($class) {
        include APP_URL.$class.'.php';
    }

    spl_autoload_register('autoloader');
?>