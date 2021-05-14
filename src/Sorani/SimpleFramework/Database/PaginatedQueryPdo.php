<?php

// declare(strict_types=1);

namespace Sorani\SimpleFramework\Database;

use Pagerfanta\Adapter\AdapterInterface;

class PaginatedQueryPdo implements AdapterInterface
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * Query to execute
     * @var string
     */
    private $query;

    /**
     * Query for the count of rows
     * @var string
     */
    private $countQuery;

    /**
     * Name of the Entity
     * @var string
     */
    private $entityName;

    /**
     * Parameters for prepared queries
     * @var array
     */
    private $params;

    /**
     * PaginatedQueryPdo contructor
     *
     * @param  \PDO $pdo
     * @param  string $query Query enabling to retrieve X results
     * @param  string $countQuery Query enabling the count of the total number of results
     * @param array $options Params for prepared queries
     * @return void
     */
    public function __construct(
        \PDO $pdo,
        $query,
        $countQuery,
        $entityName = \stdClass::class,
        array $params = []
    ) {
        $this->pdo = $pdo;
        $this->query = $query;
        $this->countQuery = $countQuery;
        $this->entityName = $entityName;
        $this->params = $params;
    }

    /**
     * Returns the number of results for the list.
     */
    public function getNbResults()
    {
        if (!empty($this->params)) {
            $statemnt = $this->pdo->prepare($this->countQuery);
            $statemnt->execute($this->params);
            return $statemnt->fetchColumn();
        }
        return (int)$this->pdo->query($this->countQuery)->fetchColumn();
    }

    /**
     * Returns an slice of the results representing the current page of items in the list.
     * @return array|\Traversable
     */
    public function getSlice($offset, $length)
    {
        $statement = $this->pdo->prepare($this->query . ' LIMIT :offset, :length;');
        foreach ($this->params as $key => $param) {
            $statement->bindValue($key, $param);
        }
        $statement->bindValue('offset', $offset, \PDO::PARAM_INT);
        $statement->bindValue('length', $length, \PDO::PARAM_INT);
        if ($this->entityName) {
            $statement->setFetchMode(\PDO::FETCH_CLASS, $this->entityName);
        }
        $statement->execute();
        return $statement->fetchAll();
    }
}
