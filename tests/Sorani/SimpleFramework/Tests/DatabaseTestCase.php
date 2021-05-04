<?php

declare(strict_types=1);

namespace Tests\Sorani\SimpleFramework\Tests;

use Error;
use PDO;
use Phinx\Config\Config;
use Phinx\Migration\Manager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class DatabaseTestCase extends TestCase
{
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * @var Manager
     */
    private $manager;

    public function setUp(): void
    {

        // create PDO instance
        $pdo = new PDO('sqlite::memory:', null, null, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        ]);

        // create Phinx to populate database
        $configArray = require('phinx.php');
        $configArray['environments']['testing'] = [
            'adapter' => 'sqlite',
            'connection' => $pdo,
        ];

        $config = new Config($configArray);
        $this->manager = new Manager($config, new StringInput(''), new NullOutput());

        $this->manager->migrate('testing');

        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
        $this->pdo = $pdo;
    }

    /**
     * Seed the Database before test
     */
    protected function seedDatabase()
    {
        $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_BOTH);
        $this->manager->seed('testing');
        $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
    }
}
