<?php

    function autoloader($class) {
        include $class.'.php';
    }

    spl_autoload_register('autoloader');
    
?>