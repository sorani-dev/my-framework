<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\TestCase;

use PDO;
use Phinx\Config\Config;
use Phinx\Migration\Manager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * Migrate and Seed Database with Phinx configuration
 */
class DatabaseTestCase extends TestCase
{

    /**
     * Seed the Database before test
     */
    protected function seedDatabase(\PDO $pdo)
    {
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_BOTH);
        $this->getManager($pdo)->seed('testing');
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
    }

    /**
     * Seed the Database before test
     * @param \PDO
     * @return void
     */
    protected function migrateDatabase(\PDO $pdo): void
    {
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_BOTH);
        $this->getManager($pdo)->migrate('testing');
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
    }

    /**
     * Get a Migration Manager
     *
     * @param  \PDO $pdo
     * @return Manager
     */
    public function getManager(\PDO $pdo): Manager
    {
        // create Phinx to populate database
        $configArray = require('phinx.php');
        $configArray['environments']['testing'] = [
            'adapter' => 'sqlite',
            'connection' => $pdo,
        ];

        $config = new Config($configArray);
        return new Manager($config, new StringInput(''), new NullOutput());
    }

    /**
     * Get a new PDO instance
     *
     * @return  PDO
     */
    public function getPdo(): \PDO
    {
        // create PDO instance
        $pdo = new PDO('sqlite::memory:', null, null, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        ]);
        return $pdo;
    }
}
