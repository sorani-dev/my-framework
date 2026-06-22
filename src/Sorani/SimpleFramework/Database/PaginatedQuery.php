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
    private QueryBuilder $queryBuilder;

    /**
     * PaginatedQuery contructor
     *
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * Returns the number of results for the list.
     *
     * @return int The number of results
     */
    public function getNbResults(): int
    {
        return $this->queryBuilder->count();
    }

    /**
     * Returns an slice of the results representing the current page of items in the list.
     *
     * @param int $offset
     * @param int $length
     * @return iterable
     */
    public function getSlice(int $offset, int $length): iterable
    {
        $query = clone $this->queryBuilder;
        return $query->limit($length)->offset($offset)->fetchAll();
    }
}
