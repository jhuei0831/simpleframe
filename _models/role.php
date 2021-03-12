<?php
    namespace _models;

    use _models\Database;
    
    class Role 
    {
        public static function create($data)
        {
            $data = (array) $data;
            return Database::table('roles')->insert($data);
        }
    }
    