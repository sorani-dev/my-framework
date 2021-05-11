<?php
require __DIR__ . '/public/index.php';

$migrations = [];
$seeds = [];

foreach ($app->getModules() as $module) {
    if ($module::MIGRATIONS) {
        $migrations[] = $module::MIGRATIONS;
    }
    if ($module::SEEDS) {
        $seeds[] = $module::SEEDS;
    }
}

return
[
    'paths' => [
        'migrations' => $migrations,
        'seeds' => $seeds,
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        // 'production' => [
        //     'adapter' => 'mysql',
        //     'host' => $app->getContainer()->get('database.host'),
        //     'name' => $app->getContainer()->get('database.name'),
        //     'user' => $app->getContainer()->get('database.username'),
        //     'pass' => $app->getContainer()->get('database.password'),
        //     'port' => '3306',
        //     'charset' => 'utf8',
        // ],
        'development' => [
            'adapter' => 'mysql',
            'host' => $app->getContainer()->get('database.host'),
            'name' => $app->getContainer()->get('database.name'),
            'user' => $app->getContainer()->get('database.username'),
            'pass' => $app->getContainer()->get('database.password'),
            'port' => '3306',
            'charset' => 'utf8',
        ],
        'testing' => [
            'adapter' => 'sqlite',
            'memory' => 'true',
            'name' => 'testing_db',
            // 'user' => 'root',
            // 'pass' => '',
            // 'port' => '3306',
            // 'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];
