<?php

    namespace _models;

    class Variable 
    {
        public static $logLevel = [
            '100' => 'debug',
            '200' => 'info',
            '250' => 'notice',
            '300' => 'warning',
            '400' => 'error',
            '500' => 'critical',
            '550' => 'alert',
            '600' => 'energency'
        ];
    } 