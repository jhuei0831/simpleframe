<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUsersTable extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('users', ['id' => false, 'primary_key' => ['id']]);
        $table->addColumn('id', 'string')
        ->addColumn('name', 'string', ['limit' => 20, 'comment' => '姓名'])
        ->addColumn('email', 'string', ['limit' => 30, 'comment' => '信箱'])
        ->addColumn('email_varified_at', 'timestamp', ['null' => true, 'default' => NULL, 'comment' => '信箱'])
        ->addColumn('auth_code', 'string', ['null' => true, 'limit' => 50, 'comment' => '信箱驗證碼'])
        ->addColumn('password', 'string', ['limit' => 100, 'comment' => '密碼'])
        ->addColumn('role', 'integer', ['limit' => 1, 'comment' => '角色'])
        ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
        ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
        ->addIndex(['email'], ['unique' => true])
        ->create();
    }

    public function down()
    {
        $this->table('users')->drop()->save();
    }
}
