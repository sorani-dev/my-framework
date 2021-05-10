<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Database;

use Pagerfanta\Adapter\AdapterInterface;
use Sorani\SimpleFramework\Database\Query\QueryBuilder;

class PaginatedQuery implements AdapterInterface
{
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * PaginatedQuery contructor
     *
     * @param  QueryBuilder
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    /**
     * Returns the number of results for the list.
     */
    public function getNbResults()
    {
        return $this->queryBuilder->count();
    }

    /**
     * Returns an slice of the results representing the current page of items in the list.
     * @return array|\Traversable
     */
    public function getSlice($offset, $length): \Traversable
    {
        $query = clone $this->queryBuilder;
        return $query->limit($length)->offset($offset)->fetchAll();
    }
}
