<?php

// declare(strict_types=1);

use Phinx\Db\Table\Column;
use Phinx\Migration\AbstractMigration;

final class CreateUsersTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->table('users', ['collation' => 'utf8mb4_unicode_ci'])
                ->addColumn('username', 'string')
                ->addColumn('email', 'string')
                ->addColumn('password', 'string')
                ->addIndex(['email', 'username'], ['unique' => true])
                ->create();
    }
}
