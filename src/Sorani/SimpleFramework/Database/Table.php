<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Database;

use Pagerfanta\Pagerfanta;
use Sorani\SimpleFramework\Database\EntityInterface;
use Sorani\SimpleFramework\Database\Exception\NoRecordFoundException;
use Sorani\SimpleFramework\Database\PaginatedQuery;
use stdClass;

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
     * @var \PDO
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
     * Paginate the items
     *
     * @param  int $perPage number of results to display per page
     * @return Pagerfanta
     */
    public function findPaginated(int $perPage, int $currentPage): Pagerfanta
    {
        $query = new PaginatedQuery(
            $this->pdo,
            $this->paginationQuery(),
            "SELECT COUNT(*) FROM {$this->table}",
            $this->entity
        );
        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    /**
     * SQL SELECT query statement as text, used for the pagination
     *
     * @return string
     */
    protected function paginationQuery(): string
    {
        return 'SELECT * FROM ' . $this->table;
    }

    /**
     * Find a single item by its ID or null if not found
     *
     * @param  int $id
     * @return EntityInterface|\stdClass|null
     * @throws NoRecordFoundException
     */
    public function find(int $id)
    {
        return $this->fetchOrFail("SELECT * FROM {$this->table} WHERE id = ?;", [$id]);
    }

    /**
     * Retrieve all records
     *
     * @return EntityInterface
     */
    public function findAll(): array
    {
        $statement = $this->pdo->query("SELECT * FROM {$this->table};");

        if ($this->entity) {
            $statement->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        } else {
            $statement->setFetchMode(\PDO::FETCH_OBJ);
        }
        return $statement->fetchAll();
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
    public function findBy(string $field, string $value)
    {
        return $this->fetchOrFail("SELECT * FROM {$this->table} WHERE {$field}=?;", [$value]);
    }

    /**
     * Retrieve a list of data as key value pair from the records
     * (eg: ['key1' => 'value1','key2' => 'value2', ])
     *
     * @return array
     */
    public function findAsList(): array
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
    public function count(): int
    {
        return (int)$this->fetchColumn("SELECT COUNT(id) FROM {$this->table};");
        // return $count !== false ? (int)$count : -1;
    }

    /**
     * Update a record in the database
     *
     * @param  int $id
     * @param  array $params
     * @return bool
     */
    public function update(int $id, array $params): bool
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
    public function insert(array $params): bool
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
    public function delete(int $id): bool
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
    public function exists(int $id): bool
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
    public function getEntity(): string
    {
        return $this->entity;
    }

    /**
     * Get the PDO instance
     *
     * @return  \PDO
     */
    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    /**
     * Get table name in database (eg: 'posts')
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Execute a query and retrieve the first result
     *
     * @param  string $query The query statement
     * @param array $params The parameters
     * @return EntityInterface|\stdClass|null
     * @throws NoRecordFoundException
     */
    protected function fetchOrFail(string $query, array $params = [])
    {
        $statement = $this->pdo->prepare($query);
        $statement->execute($params);
        if ($this->entity) {
            $statement->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        }

        $record = $statement->fetch();
        if ($record === false) {
            throw new NoRecordFoundException();
        }
        return $record;
    }

    /**
     * Execute a query and retrieve the first column
     *
     * @param  string $query The query statement
     * @param array $params The parameters
     * @return mixed
     */
    protected function fetchColumn(string $query, array $params = [])
    {
        $statement = $this->pdo->prepare($query);
        $statement->execute($params);
        if ($this->entity) {
            $statement->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        }
        return  $statement->fetchColumn();
    }
}
