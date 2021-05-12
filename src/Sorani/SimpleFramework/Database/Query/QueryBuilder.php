<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Database\Query;

use Pagerfanta\Pagerfanta;
use Sorani\SimpleFramework\Database\EntityInterface;
use Sorani\SimpleFramework\Database\Exception\NoRecordFoundException;
use Sorani\SimpleFramework\Database\PaginatedQuery;

/**
 * Build a query with a fluent interface
 */
class QueryBuilder implements \IteratorAggregate
{
    /**
     * constants for queryType
     */
    public const SELECT = 0;
    public const INSERT = 1;
    public const UPDATE = 2;
    public const DELETE = 3;

    public const ORDERBY_ASC = "ASC";
    public const ORDERBY_DESC = "DESC";

    public const JOIN_LEFT = 'LEFT';
    public const JOIN_RIGHT = 'RIGHT';
    public const JOIN_INNER = 'INNER';

    protected const RESULT_PER_PAGE = 12;

    /**
     * Query type
     *
     * @var int
     */
    private $queryType = self::SELECT;

    /**
     * Alias already used
     *
     * @var string[]
     */
    private $knownAliases = [];

    /**
     * SELECT field1, ...
     *
     * @var array
     */
    private $fields = [];

    /**
     * FROM table1...
     *
     * @var array
     */
    private $from = [];

    /**
     * WHERE
     *
     * @var array
     */
    private $where = [];
    /**
     * ORDER BY
     *
     * @var array
     */
    private $orderBy = [];


    /**
     * GROUP BY
     *
     * @var array
     */
    private $groupBy = [];


    /**
     * HAVING
     *
     * @var array
     */
    private $having = [];

    /**
     * LIMIT
     *
     * @var string
     */
    private $limit = 0;

    /**
     * OFFSET if LIMIT exists
     *
     * @var int
     */
    private $offset = 0;

    /**
     * JOIN
     *
     * @var array
     */
    private $join = [];

    /**
     *
     * @Var array
     */
    private $joinByString = [];

    /**
     * UNION
     *
     * @var array
     */
    private $union = [];

    /**
     * PDO instance
     *
     * @var \PDO
     */
    private $pdo;

    /**
     * Parameters to inject the the query is executed if any parameter needed
     *
     * @var array
     */
    private $parameters = [];

    /**
     * Entity instantiated
     *
     * @var mixed
     */
    private $entity;

    /**
     * QueryBuilder Constructor
     * Include PDO but it is not required
     *
     * @param \PDO|null $pdo
     */
    public function __construct(?\PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }

    /**
     * FROM statement
     *
     * @param  string $table table to select From
     * @param  string|null $alias table alias if needed
     * @return self
     */
    public function from(string $table, ?string $alias = null): self
    {
        if ($alias) {
            $this->from[$alias] = $table;
        } else {
            $this->from[] = $table;
        }
        return $this;
    }

    /**
     * SELECT fields
     *
     * @param  string $fields Alist of fields to select
     * @return self
     */
    public function fields(string ...$fields): self
    {
        $this->fields = $fields;
        return $this;
    }


    /**
     * SELECT fields adding fields to select
     *
     * @param  string $fields Alist of fields to select
     * @return self
     */
    public function addFields(string ...$fields): self
    {
        if ($this->fields) {
            $this->fields = array_merge($this->fields, $fields);
        } else {
            $this->fields = $fields;
        }
        return $this;
    }

    /*
    * JOIN Statement
    *
    * @param  string $foreignTable
    * @param  string $conditions
    * @param  string $joinType
    * @return self
    */
    public function joinByString(string $foreignTable, string $conditions, string $joinType = self::JOIN_LEFT): self
    {
        $this->joinByString[$joinType][] = [$foreignTable, $conditions];
        return $this;
    }

    /**
     * INNER JOIN
     *
     * @param  string $foreignTable
     * @param  string|null $alias null for no alias
     * @param  string $conditions
     * @return self
     */
    public function innerJoin(string $foreignTable, string $alias, string $conditions): self
    {
        return $this->joinByString(
            $foreignTable . (null !== $alias ? ' AS ' . $alias : ''),
            $conditions,
            self::JOIN_INNER
        );
    }

    /**
     * WHERE
     *
     * @param  string $conditions The conditions will be linked with an AND
     * @return self
     */
    public function where(string ...$conditions): self
    {
        $this->where = array_merge($this->where, $conditions);
        return $this;
    }

    /**
     * field IN ???
     *
     * @param  string $field
     * @param  mixed  $values (scalar of QueryBuilder instance)
     * @return $this
     */
    public function in($field, $values): self
    {
        if (is_array($values)) {
            if (is_numeric($values[0])) {
                $this->where[] = $field . ' IN (' . implode(', ', $values) . ')';
            } else {
                $this->where[] = $field . ' IN ("' . implode('", "', $values) . '")';
            }
        } elseif ($values instanceof QueryBuilder) {
            $query = clone $values;
            $query = (string)$query;
            $this->where[] = $field . ' IN ( ' . rtrim($query, ';') . ' )';
        }
        return $this;
    }


    /**
     * ORDER BY
     * orderBy(field, direction)
     * orderBy(field)
     * orderBy(["field direction1", "field2 direction2", ...])
     *
     * @param  string|array $orderBy field to order
     * @param string|null $direction direction to sort (ASC, DESC)
     * @return QueryBuilder
     */
    public function orderBy($orderBy, ?string $direction = null): self
    {
        if (is_string($orderBy)) {
            if (null !== $direction) {
                $direction = ' ' . $direction;
            }
            $orderBy = $orderBy . $direction;
            $this->orderBy[] = $orderBy;
        } else {
            $this->orderBy = array_merge($this->orderBy, $orderBy);
        }

        return $this;
    }

    /**
     * LIMIT: the number of rows to be returned
     *
     * @param  int $limit
     * @return QueryBuilder
     * @throws \Exception
     */
    public function limit(int $limit): self
    {
        if (!is_int($limit)) {
            throw new \Exception('LIMIT must be an integer.');
        }
        $this->limit = $limit;
        return $this;
    }

    /**
     * OFFSET:  pick from row number $offset
     * If offset is zero, then it is not taken in account in the resulting query
     *
     * @param  int $offset
     * @return QueryBuilder
     * @throws \Exception
     */
    public function offset($offset = 0)
    {
        if (!is_int($offset)) {
            throw new \Exception('OFFSET must be an integer.');
        }
        $this->offset = $offset;
        return $this;
    }

    /**
     * GROUP BY ...
     *
     * @param  mixed $groupBy
     * @return QueryBuilder
     */
    public function groupBy($groupBy): self
    {
        $this->groupBy[] = $groupBy;
        return $this;
    }

    /**
     * HAVING ...
     *
     * @param  string $groupBy
     * @return QueryBuilder
     */
    public function having(string ...$having): self
    {
        $this->having = array_merge($this->having, $having);
        return $this;
    }

    /**
     * Set parameters for query
     *
     * @param  array $parameters Parameters: [':a', $val]
     * @param  bool  $replace replace parameters
     * @return $this
     */
    public function params(array $parameters = [], ?bool $replace = false): self
    {
        if (empty($parameters)) {
            $this->parameters = $parameters;
        } else {
            // if ($replace) {
            $this->parameters = array_merge($this->parameters, $parameters);
            // } else {
            //     $this->parameters += $parameters;
            //     }
        }
        return $this;
    }

    /**
     * Get the query parameters
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Add an Entity by its name to hydrate later
     *
     * @param  string $entity
     * @return $this
     */
    public function into(string $entity): self
    {
        $this->entity = $entity;
        return $this;
    }


    /**
     * retrieve a result
     *
     * @return mixed|bool If no result returns false
     */
    public function fetch()
    {
        $record = $this->execute()->fetch(\PDO::FETCH_ASSOC);
        if (false === $record) {
            return false;
        }
        if ($this->entity) {
            return (Hydrator::getInstance())->hydrate($record, $this->entity);
        }
        return $record;
    }

    /**
     * Retrieve a result or throw an exception if no record found
     *
     * @return \Traversable|EntityInterface|mixed
     * @throws NoRecordFoundException
     */
    public function fetchOrFail()
    {
        $record = $this->fetch();
        if (false === $record) {
            throw new NoRecordFoundException();
        }
        return $record;
    }


    /**
     * Retrieve all rows from the query
     *
     * @return QueryResult
     */
    public function fetchAll(): QueryResult
    {
        return new QueryResult($this->execute()->fetchAll(\PDO::FETCH_ASSOC), $this->entity);
    }


    /**
     * Get an hydrated entity from the query
     *
     * @param  int $index
     * @return EntityInterface
     */
    public function get(int $index): EntityInterface
    {
        if ($this->entity) {
            return Hydrator::getInstance()->hydrate($this->fetchAll()[$index], $this->entity);
        }
        return $this->entity;
    }

    /**
     * Count the number of rows (cloned from the QueryBuilder instance)
     *
     * @param  string $field
     * @return int Number of rows found.
     */
    public function count($field = '*'): int
    {
        $query = clone $this;
        if (null === $field) {
            $from = $this->from;
            reset($from);
            $values = current($from);
            $field = $values . '.*';
        }

        //        $table = $query->from[0];
        $query->fields = [];
        $query->fields('COUNT(' . $field . ')');
        return (int)$query->execute()->fetchColumn();
    }

    /**
     * Execute a query on the QueryBuilder object
     *
     * @return \PDOStatement
     */
    protected function execute(): \PDOStatement
    {
        $query = $this->__toString();
        if (!empty($this->parameters)) {
            $statement = $this->pdo->prepare($query);
            $statement->execute($this->parameters);
            return $statement;
        }
        return $this->pdo->query($query);
    }




    /**
     * Paginate the results
     *
     * @param  int $nbPerPage
     * @param  int $currentPage
     * @return Pagerfanta
     */
    public function paginate(int $nbPerPage = self::RESULT_PER_PAGE, int $currentPage = 1): Pagerfanta
    {
        $pager = new PaginatedQuery($this);
        return (new Pagerfanta($pager))->setMaxPerPage($nbPerPage)->setCurrentPage($currentPage);
    }

    /**
     * Get the Query as a string (__toString)
     *
     * @return string
     */
    public function __toString(): string
    {
        $parts = [];
        if ($this->queryType === self::SELECT) {
            $parts[] = 'SELECT';
            $parts[] = !empty($this->fields) ? implode(', ', $this->fields) : '*';
            $parts[] = 'FROM ' . $this->buildFrom();
            if (!empty($this->joinByString)) {
                $parts[] = $this->buildJoin();
            }
            if (!empty($this->where)) {
                $parts[] = 'WHERE';
                $parts[] = '(' . implode(') AND (', $this->where) . ')';
            }
            if (!empty($this->orderBy)) {
                $parts[] = 'ORDER BY';
                $parts[] = implode(', ', $this->orderBy);
            }

            if ($this->limit > 0) {
                $parts[] = 'LIMIT ' . $this->limit;
            }

            if ($this->offset > 0) {
                $parts[] = 'OFFSET ' . $this->offset;
            }

            if (!empty($this->groupBy)) {
                $parts[] = 'GROUP BY';
                $parts[] = implode(', ', $this->groupBy);
            }


            if (!empty($this->having)) {
                $parts[] = 'HAVING';
                $parts[] = implode(', ', $this->having);
            }
        }
        return implode(' ', $parts) . ';';
    }

    /**
     * Alias of __toString
     *
     * @return string
     */
    public function queryToString(): string
    {
        return $this->__toString();
    }

    /**
     * Build: FROM TABLE1, [TABLE2, [...]]
     *
     * @return string
     */
    private function buildFrom(): string
    {
        $from = [];

        foreach ($this->from as $key => $value) {
            if (is_string($key)) {
                $from[] = $value . ' AS ' . $key;
                $alias = $key;
                $this->knownAliases[$key] = true;
            } else {
                $from[] = $value;
                $alias = $value;
            }
        }
        return implode(', ', $from);
    }

    /**
     * Build and return JOIN ... ON...
     *
     * @return string
     */
    protected function buildJoin(): string
    {
        $joinString = [];
        if (!empty($this->joinByString)) {
            foreach ($this->joinByString as $type => $table) {
                foreach ($table as $join) {
                    $joinString[] = strtoupper($type) . ' JOIN ' . $join[0] . ' ON ' . $join[1];
                }
            }
        }
        return implode(' ', $joinString);
    }


    /**
     * getIterator
     *
     * IteratorAggregate implementation
     * {@inheritdoc}
     *
     * @return QueryResult
     */
    public function getIterator(): QueryResult
    {
        return $this->fetchAll();
    }
}
