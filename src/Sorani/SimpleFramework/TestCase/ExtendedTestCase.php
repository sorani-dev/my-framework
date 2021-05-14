<?php

namespace Sorani\SimpleFramework\TestCase;

use Faker\Factory;
use PHPUnit\Framework\TestCase;

/**
 * Helper methods for PHPUnit testing using TestCase
 */
class ExtendedTestCase extends TestCase
{
    /**
     *
     * @var \PDO
     */
    protected $pdo;

    /**
     * String to trim (not the base trim functionality
     *
     * @param  string $string
     * @return string
     */
    protected function trim($string)
    {
        $lines = explode("\n", $string);
        $lines = array_map('trim', $lines);
        return implode(' ', $lines);
    }

    /**
     * Method added
     * Similar but not strictly equals strings
     *
     * @param string $expected
     * @param string $actual
     * @return void
     */
    public function assertSimilar($expected, $actual)
    {
        $this->assertEquals($this->trim($expected), $this->trim($actual));
    }

    /**
     * Mocks an abstract class without requiring constructor parameters.
     * Provides a nice workaound for mocking abstract methods.
     *
     * @param string $class   Class to mock
     * @param array  $methods Specific methods to mock.
     *                        Mocks all methods by default.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject Mock instance
     */
    public function abstractMock($class, $methods = [])
    {
        if (empty($methods)) {
            $reflection = new \ReflectionClass($class);
            foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                $methods[] = $method->getName();
            }
        }

        return $this->getMockForAbstractClass($class, [], '', false, false, true, $methods);
    }

    /**
     * Sets method expectations
     *
     * @param \PHPUnit_Framework_MockObject_MockObject $mock           Mocked instance
     * @param string                                   $method         Method to set expectations for
     * @param mixed                                    $return         What the method should return.
     *                                                                 If this is a Callable, e.g. a
     *                                                                 function then it will be
     *                                                                 teated as returnCallback ()
     *
     * @param array                                    $with           Array of expected arguments.
     *                                                                 The expectations are set to
     *                                                                 be strictly equal so it's
     *                                                                 safe to pass instances here.
     *
     * @param integer $at                                              The position at which the method is expected
     *                                                                 to be called.
     *                                                                 If this is null then all other expectations
     *                                                                 will apply to all calls
     *
     * @param boolean $returnCallable                                  Whether to return $return
     *                                                                 even if it is a callback.
     *                                                                 Used for mocking methods which may return
     *                                                                 functions.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject Mock instance
     */
    public function method($mock, $method, $return, $with = null, $at = null, $returnCallable = false)
    {
        $expects = $at === null ? $this->any() : $this->at($at);
        $method = $mock
            ->expects($expects)
            ->method($method);

        if ($with !== null) {
            foreach ($with as $key => $value) {
                $with[$key] = $this->identicalTo($value);
            }

            $method = call_user_func_array([$method, 'with'], $with);
        }

        $method->will($this->returnValue($return));

        if (!$returnCallable && is_callable($return)) {
            $method->will($this->returnCallback($return));
        } else {
            $method->will($this->returnValue($return));
        }
    }

    /**
     * Sets a protected property on a given object via reflection
     *
     * @param string $object   - Name of the object in which protected value is being modified
     * @param string $property - property on instance being modified
     * @param mixed  $value    - new value of the property being modified
     *
     * @return void
     */
    public function setProtectedProperty($object, $property, $value)
    {
        $reflection = new \ReflectionClass($object);
        $reflectionProperty = $reflection->getProperty($property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $value);
    }


    /**
     * Create Sqlite Table Comments
     *
     * @return \PDO
     */
    protected function makeSqliteComments()
    {
        $db = new \PDO('sqlite:memory:', null, null, []);
        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $db->exec(
            '
        CREATE TABLE  IF NOT EXISTS comments (
	"id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	"username" TEXT NOT NULL,
	"email" TEXT NOT NULL,
	"content" TEXT NOT NULL,
	"created" TEXT NOT NULL DEFAULT "0000-00-00 00:00:00.000",
	"ref" TEWT NOT NULL,
	"ref_id" INTEGER  NOT NULL DEFAULT "0",
	"parent_id" INTEGER NOT NULL DEFAULT "0");'
        );
        return $this->pdo = $db;
    }

    /**
     * Seed Sqlite Table Comments
     *
     * @param int $numberOfSeeds
     * @return void
     */
    protected function seed($numberOfSeeds = 100)
    {
        $faker = Factory::create();
        for ($i = 1; $i <= $numberOfSeeds; $i++) {
            $data = [
                $faker->userName,
                $faker->email,
                $faker->text(200),
            //                DateTime::iso8601(),
                $faker->date('Y-m-d H:i:s', $faker->dateTimeBetween()),
                $faker->numberBetween(1, 3),
                $faker->numberBetween(1, 10),
            ];
            $s = $this->pdo->prepare(
                'INSERT INTO comments
                                ("username", "email", "content", "created", "ref", "ref_id")
                                VALUES (?, ?, ?, ?, ?, ?);'
            );
            $s->execute($data);
            $this->lastId = $this->pdo->lastInsertId();
        }

        $this->lastId = $this->pdo->lastInsertId();
        $this->lastId = $i + 1;
    }

    /**
     * Create the test database and get  back an inqstance of PDO for this table
     *
     * TABLE comments
     * ("id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
     * "name" TEXT NOT NULL)
     *
     * @return PDO
     */
    protected function getTestDatabase()
    {
        // create PDO instance
        $pdo = new \PDO('sqlite::memory:', null, null, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        ]);

        $pdo->exec('CREATE TABLE comments ("id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, "name" TEXT NOT NULL);');
        return $pdo;
    }

    /**
     * Insert into test database
     *
     * @param  \PDO $pdo
     * @param  string $names names to add to the test database
     * @return void
     */
    protected function makeInsertTestDatabase(\PDO $pdo, ...$names)
    {
        foreach ($names as $name) {
            $s = $pdo->prepare('INSERT INTO comments (name) VALUES (?);');
            $s->execute([$name]);
        }
    }
}
