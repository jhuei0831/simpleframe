<?php
	namespace _models;
	
	use PDO;
	use Exception;

	class Database
	{
		private $select = array();
		private $table;
		private $whereClause;
		private $limit;
		private $query;

	    public static function connection()
		{
			// 資料庫預設值
			$host     = $_ENV['DB_HOST'];
			$database = $_ENV['DB_DATABASE'];
			$account  = $_ENV['DB_USERNAME'];
			$password = $_ENV['DB_PASSWORD'];
			$charset  = $_ENV['DB_CHARSET'];

			$dsn = "mysql:host={$host};dbname={$database}";
			if($charset != "" && $charset != null)
				$dsn .= ";charset={$charset}";

			try
			{
				$connection = new PDO(
					$dsn,
					$account,
					$password,
					array(
						PDO::ATTR_EMULATE_PREPARES => false,
						PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
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
		public function select() {
			$this->select = func_get_args();
			return $this;
		}

		/**
		 * 設定選擇的資料表
		 */
		public function table($table)
		{
			$this->table = $table;
			return $this;
		}

		/**
		 * 設定條件
		 */
		public function where($where) {
			$this->whereClause = $where;
			return $this;
		}
					
		/**
		 * limit
		 *
		 * @param integer $limit 
		 * @return void
		 */
		public function limit($limit) {
			$this->limit = $limit;
			return $this;
		}
	
		public function result() {
			$query[] = "SELECT";
			// 如果select空值或*字號，則取全部
			if (empty($this->select) || $this->select == '*') {
				$query[] = "*";  
			}
			else {
				$query[] = join(', ', $this->select);
			}
	
			$query[] = "FROM";
			$query[] = $this->table;
	
			if (!empty($this->whereClause)) {
				$query[] = "WHERE";
				$query[] = $this->whereClause;
			}
	
			if (!empty($this->limit)) {
				$query[] = "LIMIT";
				$query[] = $this->limit;
			}
			$this->query = join(' ', $query);
			return $this;
		}

		/**
		 * PDO取全部的值
		 */
		public function getAll()
		{
			$db = self::connection();
			$sth = $db->prepare($this->query);
			$sth->execute();
			return $sth->fetchAll(PDO::FETCH_ASSOC);
		}
		
		/**
		 * PDO取單一的值
		 */
		public function getOne()
		{
			$db = self::connection();
			$sth = $db->prepare($this->query);
			$sth->execute();
			return $sth->fetch(PDO::FETCH_ASSOC);
		}

		public function find($id)
		{
			return $this->where("id = {$id}")->result()->getOne();
		}

		public function get()
		{
			return $this->result()->getAll();
		}
	}
?>