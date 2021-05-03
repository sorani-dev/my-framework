<?php

declare(strict_types=1);

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Db\Table\Column;
use Phinx\Migration\AbstractMigration;

final class CreatePostsTable extends AbstractMigration
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
    public function change(): void
    {
        $this->table('posts')
                ->addColumn('name', Column::STRING)
            ->addColumn('slug', Column::STRING)
            ->addColumn('content', Column::TEXT, ['limit' => MysqlAdapter::TEXT_LONG])
            ->addColumn('created_at', Column::DATETIME)
            ->addColumn('updated_at', Column::DATETIME)
            ->create();
    }
}
