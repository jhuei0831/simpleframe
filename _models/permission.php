<?php
    namespace _models;

    use _models\Database;

    class Permission
    {
        public static function create($data)
        {
            $data = (array) $data;
            return Database::table('permissions')->insert($data);
        }

        public static function permission_belong_roles($permission_name)
        {
            $roles = Database::table('roles')
                ->select('roles.id', 'roles.name')
                ->join('role_has_permissions', 'role_has_permissions.role_id = roles.id')
                ->join('permissions', 'permissions.id = role_has_permissions.permission_id')
                ->where("permissions.name = '".$permission_name."'")
                ->get();
            return $roles;
        }
    }
    