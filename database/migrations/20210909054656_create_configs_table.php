<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateConfigsTable extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('configs', ['id' => false, 'primary_key' => ['id']]);
        $table->addColumn('id', 'string')
        ->addColumn('isOpen', 'boolean', ['default' => 1, 'comment' => '網站是否開放'])
        ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
        ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
        ->create();
    }

    public function down()
    {
        $this->table('configs')->drop()->save();
    }
}
