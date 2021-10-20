<?php

    namespace _models\Log;
    
	use Jenssegers\Agent\Agent;
	use Kerwin\Core\Support\Facades\Session;
	use Monolog\Logger;
    use Monolog\Handler\AbstractProcessingHandler;

    class PDOHandler extends AbstractProcessingHandler
	{
		private $agent;
		private $initialized = false;
		private $pdo;
		private $statement;

		public function __construct($pdo, $level = Logger::DEBUG, bool $bubble = true)
		{
			$this->agent = new Agent();
			$this->pdo = $pdo;
			parent::__construct($level, $bubble);
		}

		protected function write(array $record): void
		{
			if (!$this->initialized) {
				$this->initialize();
			}

			$this->statement->execute(array(
				'channel' 	=> $record['channel'],
				'user' 		=> is_null(Session::get('USER_ID')) ? 'client' : Session::get('USER_ID'),
				'ip' 		=> $this->getIp(),
				'platform' 	=> $this->agent->platform(),
				'browser' 	=> $this->agent->browser(),
				'level' 	=> $record['level'],
				'message' 	=> $record['message'],
				'context' 	=> json_encode($record['context'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
			));
		}

		private function getIp()
        {
            if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
				$ip = $_SERVER["HTTP_CLIENT_IP"];
			} 
			elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
				$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
			} 
			elseif (!empty($_SERVER["REMOTE_ADDR"])) {
				$ip = $_SERVER["REMOTE_ADDR"];
			}
			else {
				$ip = '';
			}

			return $ip;
        }

		private function initialize()
		{

			$this->statement = $this->pdo->prepare(
				'INSERT INTO logs (channel, user, ip, platform, browser, level, message, context) VALUES (:channel, :user, :ip, :platform, :browser, :level, :message, :context)'
			);

			$this->initialized = true;
		}
	}
    