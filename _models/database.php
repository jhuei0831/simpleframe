<?php
	namespace _models;

	use PDO;
	use Exception;

	use _models\Message;
	use _models\Security;

	class Database
	{
		private static $select = array();
		private static $table;
		private static $where;
		private static $limit;
		private static $query;

		/**
		 * 與資料庫進行連線
		 */
	    public static function connection()
		{
			// 資料庫預設值
			$host     = $_ENV['DB_HOST'];
			$database = $_ENV['DB_DATABASE'];
			$account  = $_ENV['DB_USERNAME'];
			$password = $_ENV['DB_PASSWORD'];
			$charset  = $_ENV['DB_CHARSET'];

			$dsn = "mysql:host={$host};dbname={$database}";
			if($charset != "" && $charset != null){
				$dsn .= ";charset={$charset}";
			}
				
			try
			{
				$connection = new PDO(
					$dsn,
					$account,
					$password,
					array(
						PDO::ATTR_EMULATE_PREPARES => false,
						PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
						PDO::MYSQL_ATTR_INIT_COMMAND=>"SET sql_mode='TRADITIONAL';" 
					)
				);
				if($charset != "" && $charset != null)
				{
					$connection->exec("SET CHARACTER SET {$charset}");
					$connection->exec("SET NAMES {$charset}");
				}
				return $connection;
			}
			catch(Exception $ex)
			{
				self::connection();
			}
		}

		/**
		 * 設定選擇的欄位
		 */
		public static function select() {
			static::$select = func_get_args();
			return new static;
		}

		/**
		 * 設定選擇的資料表
		 */
		public static function table($table)
		{
			static::$table = $table;
			return new static;
		}

		public static function query_select()
		{
			$query[] = "SELECT";
			// 如果select空值或*字號，則取全部
			if (empty(static::$select) || static::$select == '*') {
				$query[] = "*";  
			}
			else {
				$query[] = join(', ', static::$select);
			}
	
			$query[] = "FROM";
			$query[] = static::$table;

			if (!empty(static::$where)) {
				$query[] = "WHERE";
				$query[] = static::$where;
			}

			if (!empty(static::$limit)) {
				$query[] = "LIMIT";
				$query[] = static::$limit;
			}
			
			static::$query = join(' ', $query);
			
			return new static;
		}

		public static function Reset() {
			static::$select = array();
			static::$table = '';
			static::$where = '';
			static::$limit = '';
			static::$query = '';
		}

		/**
		 * 設定條件
		 */
		public static function where($where) {
			static::$where = $where;
			return new static;
		}
					
		/**
		 * limit
		 *
		 * @param integer $limit 
		 * @return object
		 */
		public static function limit($limit) {
			static::$limit = $limit;
			return new static;
		}

		/**
		 * PDO取全部的值
		 */
		public static function getAll()
		{
			$db = self::connection();
			$sth = $db->prepare(static::$query);
			$sth->execute();
			self::Reset();
			return $sth->fetchAll(PDO::FETCH_ASSOC);
		}
		
		/**
		 * PDO取單一的值
		 */
		public static function getOne()
		{
			$db = self::connection();
			$sth = $db->prepare(static::$query);
			$sth->execute();
			self::Reset();
			return $sth->fetch(PDO::FETCH_ASSOC);
		}
		
		/**
		 * 使用fetch找特定id資料
		 */
		public static function find($id)
		{
			return self::where("id = {$id}")->query_select()->getOne();
		}

		/**
		 * 使用fetchAll取得資料
		 */
		public static function get()
		{
			return self::query_select()->getAll();
		}
			
		/**
		 * query_insert
		 *
		 * @param  mixed $data
		 * @return iterable|object
		 */
		private static function query_insert($data)
		{
			// 輸入只能是array型態
			if (!is_array($data)) {
				throw new Exception('Insert的參數必須是array');
			}

			// CSRF驗證
			if (Security::check_csrf($data)) {
				unset($data['token']);
			}

			$column = '';
			$values = '';
			
			//表單欄位名稱→資料表欄位名稱，表單欄位資料→資料表欄位資料，去除最後的逗點
			foreach ($data as $key => $value) 
			{
				$attr = $key;
				$column .= $attr.',';
				$values .= ':'.$attr.',';
			}

			$column = substr($column, 0, -1);
			$values = substr($values, 0, -1);

			$db = self::connection();
			$sql = 'INSERT INTO '.static::$table.' ('.$column.') VALUES ('.$values.')';
			$sth = $db->prepare($sql);
			foreach ($data as $key => $value) 
			{
				$attr = $key;
				$sth->bindValue(':'.$attr, $value);	
			}
			unset($_SESSION["token"]);
			return $sth->execute();
		}

		/**
		 * insert
		 *
		 * @param  mixed $data
		 * @return iterable|object
		 */
		public static function insert($data)
		{
			try{
				return self::query_insert($data);
			}
			catch(Exception $e) {
				if (IS_DEBUG === 'TRUE') {
					Message::show_console($e->getMessage());
				}
				else{
					return false;
				}
			}
		}
	}
?>