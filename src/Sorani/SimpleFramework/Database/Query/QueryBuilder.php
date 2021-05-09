<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Database\Query;

class QueryBuilder
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

    protected const RESULT_PER_PAGE = 10;

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
     * HAVING
     *
     * @var array
     */
    private $having = [];

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
     * SET for UPDATE
     *
     * @var array
     */
    private $set = [];

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
     * Parameters to inject at the execution of this query if needed
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


    /*
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
     * WHERE
     *
     * @param  string $conditions The conditions willbe linked with an AND
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
     * @param  mixed  $values (scalar)
     * @return $this
     */
    public function in($field, $values)
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
     * LIMIT numberOfRows number of rows to be returned
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
     * OFFSET  pick from row number $offset
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
     * GROUP BY ...
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
     * @param  array $parameters
     * @param  bool  $replace
     * @return $this
     */
    public function params(array $parameters = [], ?bool $replace = false): self
    {
        if (empty($parameters)) {
            $this->parameters = $parameters;
        } else {
            if ($replace) {
                $this->parameters = array_merge($this->parameters, $parameters);
            } else {
                $this->parameters += $parameters;
            }
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
     * Count the number of rows (cloned from the QueryBuilder instance)
     *
     * @param  mixed $field
     * @return int
     */
    public function count(?string $field = 'id'): int
    {
        $query = clone $this;

        $query->fields = [];
        $query->fields('COUNT(' . $field . ')');
        return (int)$query->execute()->fetchColumn();
    }

    /**
     * Execute a query on the QueryBuilder object
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
     * __toString Get the Query a a string
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

    public function queryToString(): string
    {
        return $this->__toString();
    }

    /**
     * Build FROM TABLE1, [TABLE2, [...]]
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

    protected function buildJoin()
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
}
