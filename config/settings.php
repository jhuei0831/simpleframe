<?php 
	ini_set('session.cookie_lifetime', 0);
	// 自動載入 Composer 的套件
	require_once(__DIR__.'/autoload.php');
	use Kerwin\Core\Support\Facades\Session;
	session_start();

	//設定時區
	date_default_timezone_set($_ENV['APP_TIMEZONE']);

	// 產生驗證CRSF的Token
	if(!Session::has('token'))
	{
		Session::set('token', hash('sha256', uniqid()));
	}

	// 使用者資訊
	if (!Session::has('USER_ID')) {
		Session::set('USER_ID', NULL);
	}

	// 定義常數
	define("APP_PROTOCOL", 		isset($_SERVER["REQUEST_SCHEME"]) ? $_SERVER["REQUEST_SCHEME"] : "http");
	define("APP_DOMAIN", 		isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : "localhost");
	define("APP_FOLDER", 		$_ENV['APP_FOLDER']);
	define("APP_NAME", 			$_ENV['APP_NAME']);
	define("APP_ADDRESS",		APP_PROTOCOL."://".APP_DOMAIN."/".APP_FOLDER."/");
	define("APP_STATUS", 		strtoupper($_ENV['APP_STATUS']));
	define("IS_DEBUG", 			strtoupper($_ENV['APP_DEBUG']));
	define("PASSWORD_SECURE", 	strtoupper($_ENV['AUTH_PASSWORD_SECURITY']));
	define("EMAIL_VERIFY", 		strtoupper($_ENV['AUTH_EMAIL_VERIFY']));
	define('TOKEN',				Session::get('token'));

	// 例外清單，可以看到錯誤訊息
	$except_ip_list = array(
		"127.0.0.1",
	);

	define('EXCEPT_IP_LIST', $except_ip_list);

	// 不在清單內的關閉錯誤訊息
	if(isset($_SERVER["REMOTE_ADDR"]) && !in_array($_SERVER["REMOTE_ADDR"], EXCEPT_IP_LIST))
	{
		error_reporting(0);
	}

	// 網站設定
	define("APP_URL",		$root);
	define("APP_SRC",		APP_ADDRESS."src/");
	define("APP_IMG",		APP_SRC."img/");
	define("APP_CSS",		APP_SRC."css/");
	define("APP_JS",		APP_SRC."js/");
	define("APP_FILE",		APP_SRC."file/");
	define("APP_NODE",		APP_URL."node_modules/");

	// 是否開放網站
	/* if(!in_array($_SERVER["REMOTE_ADDR"], $except_ip_list) && APP_STATUS == 'OFF')
	{
		include_once(APP_URL.'_error/fix.php');
		exit;
	} */
?>