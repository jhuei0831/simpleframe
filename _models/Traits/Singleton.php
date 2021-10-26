<?php

    namespace _models\Traits;

    trait Singleton
    {
        /**
         * 實例
         *
         * @var null|object
         */
        private static $instance;
        
        /**
         * 取得實例
         * 
         * @return self
         */
        public static function getInstance()
        {
            if (!self::$instance) {
                self::$instance = new self();
            }
            
            return self::$instance;
        }
    }
    