<?php
    namespace _models;

    use _models\Database;
    
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
            $role = Database::table('roles')->select('id')->where("name ='{$role}'")->get();

            return count($role) > 0 ? $role[0]['id'] : false;
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
            $check = Database::table('users')->where('id ='.$_SESSION['USER_ID'].' and role ='.$role_id)->count();
            return $check > 0 ? true : false;
        }
    }
    