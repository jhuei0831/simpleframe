<?php 
	$root = '../';
	include_once($root.'_config/settings.php');

    use Kerwin\Core\Support\Facades\Message;

	// 登出功能
	unset($_SESSION['USER_ID']);

    Message::flash('登出成功', 'success');
    Message::redirect(APP_ADDRESS.'auth/login.php');
?>