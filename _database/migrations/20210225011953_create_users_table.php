<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUsersTable extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('users', ['id' => 'id']);
        $table->addColumn('name', 'string', ['comment' => '姓名'])
        ->addColumn('email', 'string', ['comment' => '信箱'])
        ->addColumn('email_varified_at', 'timestamp', ['null' => true, 'default' => NULL, 'comment' => '信箱'])
        ->addColumn('password', 'string', ['comment' => '密碼'])
        ->addColumn('role', 'integer', ['comment' => '角色'])
        ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
        ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
        ->create();
    }

    public function down()
    {
        $this->table('users')->drop()->save();
    }
}
