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
        $role = ['name' => 'admin'];
        $this->table('roles')->insert(['name' => $role['name']])->saveData();

        // 建立權限並將角色賦予權限
        $permissions = [
            'users-list',
            'users-create',
            'users-edit',
            'users-delete',
            'roles-list',
            'roles-create',
            'roles-edit',
            'roles-delete'
        ];
        foreach ($permissions as $permission) {
            $this->table('permissions')->insert(['name' => $permission])->saveData();
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
    }
}
