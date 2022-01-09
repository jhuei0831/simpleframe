<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

final class CreateLogsTable extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('logs', ['id' => 'id']);
        $table->addColumn('channel', 'string', ['limit' => 255, 'comment' => ''])
        ->addColumn('user', 'string', ['limit' => 36, 'comment' => '使用者'])
        ->addColumn('ip', 'string', ['limit' => 20, 'comment' => '使用者IP'])
        ->addColumn('platform', 'string', ['limit' => 20, 'comment' => '作業系統'])
        ->addColumn('browser', 'string', ['limit' => 20, 'comment' => '瀏覽器'])
        ->addColumn('level', 'string', ['limit' => 3, 'comment' => '訊息等級'])
        ->addColumn('message', 'text', ['comment' => '訊息'])
        ->addColumn('context', 'text', ['comment' => '內文'])
        ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
        ->create();
    }

    public function down()
    {
        $this->table('logs')->drop()->save();
    }
}
