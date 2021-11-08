<?php

use Phinx\Seed\AbstractSeed;
use Ramsey\Uuid\Uuid;
class DefaultSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run()
    {
        // 建立角色
        $roles = [
            'admin',
            'guest'
        ];
        foreach ($roles as $key => $role) {
            $this->table('roles')->insert(['name' => $role])->saveData();
        }    

        // 建立權限並將角色賦予權限
        $permissions = [
            ['name' => 'manage-index', 'description' => '訪問後台'],
            ['name' => 'config-edit', 'description' => '設定修改'],
            ['name' => 'logs-list', 'description' => 'Log清單'],
            ['name' => 'users-list', 'description' => '使用者清單'],
            ['name' => 'users-create', 'description' => '使用者新增'],
            ['name' => 'users-edit', 'description' => '使用者修改'],
            ['name' => 'users-delete', 'description' => '使用者刪除'],
            ['name' => 'roles-list', 'description' => '角色清單'],
            ['name' => 'roles-create', 'description' => '角色新增'],
            ['name' => 'roles-edit', 'description' => '角色修改'],
            ['name' => 'roles-delete', 'description' => '角色刪除'],
            ['name' => 'permissions-list', 'description' => '權限清單'],
            ['name' => 'permissions-create', 'description' => '權限新增'],
            ['name' => 'permissions-edit', 'description' => '權限修改'],
            ['name' => 'permissions-delete', 'description' => '權限刪除'],
        ];
        foreach ($permissions as $permission) {
            $this->table('permissions')->insert(['name' => $permission['name'], 'description' => $permission['description']])->saveData();
        }

        for ($i=1; $i <= count($permissions); $i++) { 
            $this->table('role_has_permissions')->insert(['role_id' => 1, 'permission_id' => $i])->saveData();
        }

        // 建立管理者
        $admin = [
            'id' => Uuid::uuid4(),
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => md5('password'),
            'role' => 1
        ];
        
        $this->table('users')->insert($admin)->saveData();

        // 建立設定
        $config = [
            'id' => 'simpleframe',
            'isOpen' => 1
        ];
        
        $this->table('configs')->insert($config)->saveData();
    }
}
