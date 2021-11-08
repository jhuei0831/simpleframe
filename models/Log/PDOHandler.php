<?php

    namespace models\Log;
    
	use Jenssegers\Agent\Agent;
	use Kerwin\Core\Support\Facades\Database;
	use Kerwin\Core\Support\Facades\Session;
	use Monolog\Logger;
    use Monolog\Handler\AbstractProcessingHandler;

class PDOHandler extends AbstractProcessingHandler
	{		
		/**
		 * Agent instance
		 *
		 * @var Jenssegers\Agent\Agent
		 */
		private $agent;		
					
		/**
		 * statement
		 *
		 * @var array
		 */
		private $statement = [];

		public function __construct($level = Logger::DEBUG, bool $bubble = true)
		{
			$this->agent = new Agent();
			parent::__construct($level, $bubble);
		}

		protected function write(array $record): void
		{
			$this->statement = array(
				'channel' 	=> $record['channel'],
				'user' 		=> is_null(Session::get('USER_ID')) ? 'client' : Session::get('USER_ID'),
				'ip' 		=> $this->getIp(),
				'platform' 	=> $this->agent->platform(),
				'browser' 	=> $this->agent->browser(),
				'level' 	=> $record['level'],
				'message' 	=> $record['message'],
				'context' 	=> json_encode($record['context'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
			);

			Database::table('logs')->insert($this->statement, false);
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
	}
    