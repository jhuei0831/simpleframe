<?php 
	// 自動載入 Composer 的套件們
	if (file_exists(__DIR__.'/../vendor/autoload.php'))
	{
	    require_once(__DIR__.'/../vendor/autoload.php');

	    // 避免 env 的參數回傳空值
		function loadEnvRecursive() {
		    // 啟用 phpdotenv 套件的功能: 加載 .env 檔案
			$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/..');
			$dotenv->load();
			
			if (!isset($_ENV["DB_DATABASE"]))
			{
				return loadEnvRecursive();
			}
		}
		loadEnvRecursive();
	}
	else
	{
		echo "<script>alert('系統安裝不完全: 您尚未安裝 PHP 套件、函式庫。');</script>";
	}
?>