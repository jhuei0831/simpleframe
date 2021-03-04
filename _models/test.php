<?php
    namespace _models;
    
    class Test
    {
        public static $val;

        public static function add($var){
            static::$val+=$var;
            return new static;
        }

        public static function sub($var){
            static::$val-=$var;
            return new static;
        }

        public static function out(){
            return static::$val;
        }

        public static function init($var){
            static::$val=$var;
            return new static;      
        }

        //跳出JS對話框
        public static function show_message($msg)
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
            echo '<script>alert("'.$msg.'");</script>';
        }

        //跳出JS console log
        public static function show_console($msg)
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
            echo '<script>console.log("'.$msg.'");</script>';
        }
    }