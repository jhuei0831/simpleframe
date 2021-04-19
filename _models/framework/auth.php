<?php
    namespace  _models\framework;

    use _models\framework\Database as DB;

    class Auth
    {
        public static function user()
        {
            $user = DB::table($_ENV['AUTH_TABLE'])->where("id = '{$_SESSION['USER_ID']}'")->first();
            return isset($user) ? $user : false;
        }   

        public static function id()
        {
            $user = DB::table($_ENV['AUTH_TABLE'])->where("id = '{$_SESSION['USER_ID']}'")->first();
            return isset($user->id) ? $user->id : false;
        }
    }
    