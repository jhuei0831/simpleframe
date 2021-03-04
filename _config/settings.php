<?php 
	ini_set('session.cookie_lifetime', 0);
	session_start();
	// 自動載入 Composer 的套件
	require_once('autoload.php');
	
	//設定時區
	date_default_timezone_set($_ENV['APP_TIMEZONE']);

	// 載入類別
	include('_models/autoloader.php');

	// 產生驗證CRSF的Token
	if(!isset($_SESSION['token']))
	{
		$_SESSION['token'] = hash('sha256', uniqid());
	}

	// 定義常數
	define("WEB_PROTOCOL", 		isset($_SERVER["REQUEST_SCHEME"]) ? $_SERVER["REQUEST_SCHEME"] : "http");
	define("WEB_DOMAIN", 		isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : "localhost");
	define("WEB_FOLDER", 		$_ENV['APP_FOLDER']);
	define("WEB_CODE", 			$_ENV['APP_NAME']);
	define("WEB_ADDRESS",		WEB_PROTOCOL."://".WEB_DOMAIN."/".WEB_FOLDER.WEB_CODE."/");
	define("IS_DEBUG", 			strtoupper($_ENV['APP_DEBUG']));
	define('TOKEN',				$_SESSION['token']);

	// 例外清單，可以看到錯誤訊息
	$except_ip_list = array(
		//
	);

	// 不在清單內的關閉錯誤訊息
	if(isset($_SERVER["REMOTE_ADDR"]) && !in_array($_SERVER["REMOTE_ADDR"], $except_ip_list))
	{
		error_reporting(0);
	}

	// 網站設定
	define("WEB_URL",		$root);
	define("WEB_SRC",		$root."src/");
	define("WEB_IMG",		WEB_SRC."img/");
	define("WEB_CSS",		WEB_SRC."css/");
	define("WEB_JS",		WEB_SRC."js/");
	define("WEB_FILE",		WEB_SRC."file/");
?>