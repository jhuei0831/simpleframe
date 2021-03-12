<?php
    namespace _models;
    
    class Message
    {
            
        /**
         * JS前往指定URL
         *
         * @param  string $url
         * @return void
         */
        public static function redirect($url)
        {
            echo '<script>window.location="'.$url.'";</script>';
            exit(0);
        }
   
        /**
         * 跳出JS console log  
         *
         * @param  string $msg
         * @return void
         */
        public static function show_console($msg)
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
            echo '<script>console.log("'.$msg.'");</script>';
        }
     
        /**
         * 跳出JS對話框
         *
         * @param  string $msg
         * @return void
         */
        public static function show_message($msg)
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
            echo '<script>alert("'.$msg.'");</script>';
        }
    }
    