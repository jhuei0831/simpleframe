<?php
    namespace _models\framework;

    use _models\framework\Database;
    
    class Role 
    {        
        /**
         * 建立角色
         *
         * @param  mixed $data
         * @return void
         */
        public static function create($data)
        {
            $data = (array) $data;
            return Database::table('roles')->insert($data);
        }
        
        /**
         * 取得角色ID
         *
         * @param  mixed $role
         * @return string
         */
        private static function getRoleID($role)
        {
            $role = Database::table('roles')->select('id')->where("name ='{$role}'")->first();

            return $role->id ?? false;
        }

                
        /**
         * 使用者是否符合角色身分
         *
         * @param  mixed $role
         * @return void
         */
        public static function has($role)
        {
            $role_id = self::getRoleID($role);
            if ($role_id === false) {
                return false;
            }
            // var_dump($role_id);exit;
            $check = Database::table('users')->where('id ="'.$_SESSION['USER_ID'].'" and role ="'.$role_id.'"')->count();
            return $check > 0 ? true : false;
        }
    }
    