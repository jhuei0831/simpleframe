<?php
// 自動載入 Composer 的套件們
require_once(__DIR__."/_config/autoload.php");

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/_database/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/_database/seeds'
    ],
    'environments' => [
        // 'default_migration_table' => 'phinxlog',
        'default_environment' => $_ENV['APP_ENV'],
        'production' => [
            'adapter' => $_ENV['DB_CONNECTION'],
            'host' => $_ENV['DB_HOST'],
            'name' => $_ENV['DB_DATABASE'],
            'user' => $_ENV['DB_USERNAME'],
            'pass' => $_ENV['DB_PASSWORD'],
            'port' => $_ENV['DB_PORT'],
            'charset' => $_ENV['DB_CHARSET'],
        ],
        'development' => [
            'adapter' => $_ENV['DB_CONNECTION'],
            'host' => $_ENV['DB_HOST'],
            'name' => $_ENV['DB_DATABASE'],
            'user' => $_ENV['DB_USERNAME'],
            'pass' => $_ENV['DB_PASSWORD'],
            'port' => $_ENV['DB_PORT'],
            'charset' => $_ENV['DB_CHARSET'],
        ],
    ],
    'version_order' => 'creation'
];
