<?php 
	$root = '../';
	include_once($root.'_config/settings.php');

    use _models\Message as MG;

	// 登出功能
	unset($_SESSION['USER_ID']);

    MG::flash('登出成功', 'success');
    MG::redirect(APP_ADDRESS.'auth/login.php');
?>