<?php
	require __DIR__ . '/Route/function.php';

    spl_autoload_register(function ($class) {
    	$class = str_replace('\\', '/', ucfirst($class));
	    if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . "../{$class}.php")){
	        require_once (__DIR__ . DIRECTORY_SEPARATOR . "../{$class}.php");
	    };
	});
?>