<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreatePasswordResetsTable extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('password_resets', ['id' => false, 'primary_key' => ['id']]);
        $table->addColumn('id', 'string', ['comment' => '使用者id'])
        ->addColumn('password', 'string', ['comment' => '密碼'])
        ->addColumn('email_token', 'string', ['null' => false, 'comment' => '忘記密碼驗證token'])
        ->addColumn('token_updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
        ->addColumn('password_updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
        ->create();
    }

    public function down()
    {
        $this->table('password_resets')->drop()->save();
    }
}
