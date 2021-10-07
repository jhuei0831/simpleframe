<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateRoleHasPermissionsTable extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('role_has_permissions', ['id' => false, 'primary_key' => ['permission_id', 'role_id']]);
        $table->addColumn('permission_id', 'integer', ['limit' => 1, 'null' => false, 'comment' => '權限id'])
        ->addColumn('role_id', 'integer', ['limit' => 1, 'null' => false, 'comment' => '角色id'])
        ->addForeignKey('role_id', 'roles', 'id', ['delete' => 'CASCADE'])
        ->addForeignKey('permission_id', 'permissions', 'id', ['delete' => 'CASCADE'])
        ->create();
    }

    public function down()
    {
        $this->table('role_has_permissions')->drop()->save();
    }
}
