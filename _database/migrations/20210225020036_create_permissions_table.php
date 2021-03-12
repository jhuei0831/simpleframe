<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreatePermissionsTable extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('permissions', ['id' => 'id']);
        $table->addColumn('name', 'string', ['comment' => '名稱'])
        ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
        ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
        ->addIndex(['name'], ['unique' => true])
        ->create();
    }

    public function down()
    {
        $this->table('permissions')->drop()->save();
    }
}
