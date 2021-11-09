<?php

    namespace models\Log;

    use models\Log\PDOHandler;
    use Monolog\Logger;

    class Log
    {        
        /**
         * Monolog\Logger instance
         *
         * @var mixed
         */
        public $logger;
        
        public function __construct(string $channel = 'MySQL') {
            $this->logger = new Logger($channel);
            $this->logger->pushHandler(new PDOHandler());
        }
        
        /**
         * debug 100
         *
         * @param  mixed $message
         * @param  array $context
         * @return void
         */
        public function debug($message, array $context = []): void
        {
            $this->logger->debug($message, $context);
        }
        
        /**
         * error 400
         *
         * @param  mixed $message
         * @param  array $context
         * @return void
         */
        public function error($message, array $context = []): void
        {
            $this->logger->error($message, $context);
        }
        
        /**
         * info 200
         *
         * @param  mixed $message
         * @param  array $context
         * @return void
         */
        public function info($message, array $context = []): void
        {
            $this->logger->info($message, $context);
        }
        
        /**
         * warning 300
         *
         * @param  mixed $message
         * @param  array $context
         * @return void
         */
        public function warning($message, array $context = []): void
        {
            $this->logger->warning($message, $context);
        }
    }
    