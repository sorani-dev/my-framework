<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Database\Query;

use Sorani\Database\Exception\QueryException;
use Sorani\SimpleFramework\Database\EntityInterface;

class QueryResult implements \ArrayAccess, \Iterator, \Countable
{
    /**
     * Stored records
     *
     * @var array
     */
    private $records;

    /**
     * Index used for the Iterator
     *
     * @var int
     */
    private $index = 0;

    /**
     * When the record is instanciated into an entity, it is hydrated
     *
     * @var array
     */
    private $hydratedRecords = [];

    /**
     * Entity instantiated
     *
     * @var string|null
     */
    private $entity;

    /**
     * QueryResult Constructor
     *
     * @param array $records Database results
     * @param string|null $entity Entity to populate
     */
    public function __construct(array $records, ?string $entity = null)
    {
        $this->records = $records;
        $this->entity = $entity;
    }

    /**
     *
     * @return string|null
     */
    public function getEntity(): ?string
    {
        return $this->entity;
    }

    /**
     *Set the current entity
     *
     * @param string $entity
     * @return QueryResult
     */
    public function setEntity(string $entity): self
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * get a value from the records
     *
     * @param  string $key
     * @return EntityInterface|object
     */
    public function get($key)
    {
        if ($this->entity) {
            if (!isset($this->hydratedRecords[$key])) {
                $this->hydratedRecords[$key] = Hydrator::getInstance()->hydrate($this->records[$key], $this->entity);
            }
            return $this->hydratedRecords[$key];
        }
        return $this->entity;
    }

    /**
     * Return the current element
     *
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return $this->get($this->index);
    }

    /**
     * Move forward to next element
     *
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        $this->index++;
    }

    /**
     * Return the key of the current element
     *
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * Checks if current position is valid
     *
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid(): bool
    {
        return isset($this->records[$this->index]);
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind(): void
    {
        $this->index = 0;
    }

    /**
     * Whether a offset exists
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset): bool
    {
        return isset($this->records[$offset]);
    }

    /**
     * Offset to retrieve
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset
     * <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @throws QueryException
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        throw new QueryException('Cannot set a key from the DataSet.');
    }

    /**
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @throws QueryException
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        throw new QueryException('Cannot unset a key from the DataSet.');
    }

    /**
     * Count elements of an object
     *
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count(): int
    {
        return count($this->records);
    }

    /**
     *
     * @return EntityInterface[]|array
     */
    public function getRecords(): array
    {
        $items = [];
        foreach ($this->records as $key => $value) {
            // $items[$key] = $this->get($key);
            $items[$key] = $this->hydratedRecords[$key] = Hydrator::getInstance()
                ->hydrate($this->records[$key], $this->entity);
        }
        return $items;
    }
}
