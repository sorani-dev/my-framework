<?php

// declare(strict_types=1);

namespace Sorani\SimpleFramework\Database;

use Sorani\SimpleFramework\Database\EntityInterface;
use Sorani\SimpleFramework\Database\Exception\NoRecordFoundException;
use Sorani\SimpleFramework\Database\Query\QueryBuilder;
use stdClass;

/**
 * Tables managing database records and Entities
 */
class Table
{
    /**
     * Entity name (eg: Post::class)
     *
     * @var string|null EntityInterface::class
     */
    protected $entity = \stdClass::class;

    /**
     * Table name in database (eg: 'posts')
     *
     * @var string
     */
    protected $table;

    /**
     * @var \PDO|null
     */
    protected $pdo;

    /**
     * Table Cconstruct
     *
     * @param  PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Make a new Instance of the QueryBuilder
     *
     * @string|null $alias Table alias if needed else it will be the first letter of the table
     * @return QueryBuilder
     */
    protected function makeQuery($alias = null)
    {
        return (new QueryBuilder($this->pdo))->from($this->table, (isset($alias) ? $alias : $this->table[0]))
        ->into($this->entity);
    }

    /**
     * Find a single item by its ID or null if not found
     *
     * @param  int $id
     * @return EntityInterface|\stdClass|null
     * @throws NoRecordFoundException
     */
    public function find($id)
    {
        return $this->makeQuery()->where("id = :id")->params([':id' => $id])->fetchOrFail();
    }

    /**
     * Retrieve all records
     *
     * @return QueryBuilder
     */
    public function findAll()
    {
        return $this->makeQuery();
    }

    /**
     * Retrieve a record for a specific field
     *
     * @param  string $field Field name
     * @param  string $value Value to search for
     * @return EntityInterface|\stdClass|EntityInterface[]|\stdClass
     *
     * @throws NoRecordFoundException
     */
    public function findBy($field, $value)
    {
        return $this->makeQuery()->where("{$field}=:$field")->params([":{$field}" => $value])->fetchOrFail();
    }

    /**
     * Retrieve a list of data as key value pair from the records
     * (eg: ['key1' => 'value1','key2' => 'value2', ])
     *
     * @return array
     */
    public function findAsList()
    {
        $results = $this->pdo
            ->query("SELECT id, name FROM {$this->table};")
            ->fetchAll(\PDO::FETCH_NUM);
        $list = [];
        foreach ($results as $result) {
            $list[$result[0]] = $result[1];
        }
        return $list;
    }

    /**
     * Retrieve the number of records
     *
     * @return int
     */
    public function count()
    {
        return (int)$this->makeQuery()->count();
        // return $count !== false ? (int)$count : -1;
    }

    /**
     * Update a record in the database
     *
     * @param  int $id
     * @param  array $params
     * @return bool
     */
    public function update($id, array $params)
    {
        $fieldsQuery = join(' , ', array_map(function ($field) {
            return "{$field} = :{$field}";
        }, array_keys($params)));
        $statement = $this->pdo->prepare("UPDATE {$this->table} SET {$fieldsQuery} WHERE id = :id;");
        $params[':id'] = $id;
        return $statement->execute($params);
    }

    /**
     * Create a new record in the database
     *
     * @param  array $params
     * @return bool
     */
    public function insert(array $params)
    {
        $fields = array_keys($params);
        $names = join(', ', $fields);
        $values = ':' . join(' , :', array_keys($params));
        $statement = $this->pdo->prepare("INSERT INTO {$this->table} ({$names}) VALUES ({$values});");
        return $statement->execute($params);
    }

    /**
     * Delete a record from the database
     *
     * @param  int $id
     * @return bool
     */
    public function delete($id)
    {
        $statement = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id;");
        return $statement->execute([':id' => $id]);
    }

    /**
     * Check if a record exists
     *
     * @param  int $id
     * @return bool
     */
    public function exists($id)
    {
        $statement = $this->pdo->prepare("SELECT id FROM {$this->table} WHERE id = ?");
        $statement->execute([$id]);
        return $statement->fetchColumn() !== false;
    }

    /**
     * Get entity name
     *
     * @return  string
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Get the PDO instance
     *
     * @return  \PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * Get table name in database (eg: 'posts')
     *
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Set the value of pdo
     *
     * @param  \PDO  $pdo
     *
     * @return  self
     */
    public function setPdo(\PDO $pdo)
    {
        $this->pdo = $pdo;

        return $this;
    }
}
