<?php 
	$root = '../';
	include_once($root.'config/settings.php');

    use models\Auth\User;

	// 登出功能
	$user = new User;
	$user->logout();
?>