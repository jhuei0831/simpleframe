<?php 
	$root = '../';
	include_once($root.'_config/settings.php');

    use _models\Auth\User;

	// 登出功能
	$user = new User;
	$user->logout();
?>