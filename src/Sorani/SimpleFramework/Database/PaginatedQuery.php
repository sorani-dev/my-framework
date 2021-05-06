<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Database;

use Pagerfanta\Adapter\AdapterInterface;

class PaginatedQuery implements AdapterInterface
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var string
     */
    private $query;
    /**
     * @var string
     */
    private $countQuery;

    /**
     * @var string
     */
    private $entityName;

    /**
     * PaginatedQuery contructor
     *
     * @param  \PDO $pdo
     * @param  string $query Query enabling to retrieve X results
     * @param  string $countQuery Query enabling the count of the total number of results
     * @return void
     */
    public function __construct(\PDO $pdo, string $query, string $countQuery, string $entityName = \stdClass::class)
    {
        $this->pdo = $pdo;
        $this->query = $query;
        $this->countQuery = $countQuery;
        $this->entityName = $entityName;
    }
    /**
     * Returns the number of results for the list.
     */
    public function getNbResults()
    {
        return (int)$this->pdo->query($this->countQuery)->fetchColumn();
    }

    /**
     * Returns an slice of the results representing the current page of items in the list.
     * @return array|\Traversable
     */
    public function getSlice($offset, $length): array
    {
        $statement = $this->pdo->prepare($this->query . ' LIMIT :offset, :length;');
        $statement->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $statement->bindValue(':length', $length, \PDO::PARAM_INT);
        if ($this->entityName) {
            $statement->setFetchMode(\PDO::FETCH_CLASS, $this->entityName);
        }
        $statement->execute();
        return $statement->fetchAll();
    }
}
