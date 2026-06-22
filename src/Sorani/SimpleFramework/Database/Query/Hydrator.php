<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Database\Query;

use ReflectionClass;
use Sorani\Database\Exception\PropertyNotExistsException;

class Hydrator
{
    /**
     * @var Hydrator
     */
    private static $instance;

    /**
     * getInstance
     *
     * @return self
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new Hydrator();
        }
        return self::$instance;
    }

    /**
     * hydrate an object
     *
     * @param  array $values
     * @param  string|object $object
     * @return object
     */
    public function hydrate(array $values, $object): object
    {
        if (is_string($object)) {
            /** @var ReflectionClass $reflection */
            $reflection = new \ReflectionClass($object);
            if ($reflection->getConstructor() !== null) {
                $instance = $reflection->newInstance();
            } else {
                $instance = $reflection->newInstanceWithoutConstructor();
            }
        } else {
            $instance = $object;
        }
        foreach ($values as $key => $value) {
            $method = $this->getSetter($key);
            $property = lcfirst($this->getProperty($key));

            if (method_exists($instance, $method)) {
                $instance->$method($value);
            } elseif (property_exists($instance, $property)) {
                $value = $this->convertValue($instance, $property, $value);
                $instance->$property = $value;
            } else {
                // throw new PropertyNotExistsException(
                // sprintf('Property "%s" does not exist in "%s"',
                // $property, get_class($instance))
                //);
                $instance->$property = $value;
            }
        }
        return $instance;
    }

    /**
     * Get the set<Name> method for testing if existss in method in the instantiated object
     *
     * @param  string $fieldName
     * @return string
     */
    public function getSetter(string $fieldName): string
    {
        return 'set' . $this->getProperty($fieldName);
    }

    /**
     * Get the <name> of the property from the snake cased field name
     *
     * @param  string $fieldName
     * @return string in CamelCase
     */
    public function getProperty(string $fieldName): string
    {
        return implode('', array_map('ucfirst', explode('_', $fieldName)));
    }

    /**
     * Convert the value to the type of the property
     *
     * @param  object $object
     * @param  string $property
     * @param  mixed $value
     * @return mixed
     */
    public function convertValue(object $object, string $property, $value)
    {
        $reflection = new \ReflectionProperty($object, $property);
        /** @var \ReflectionNamedType|\ReflectionUnionType|\ReflectionIntersectionType|null */
        $type = $reflection->getType();

        if ($type === null) {
            return $value;
        }
        if ($type->getName() === 'string') {
            return (string) $value;
        } elseif ($type->getName() === 'int') {
            return (int) $value;
        } elseif ($type->getName() === 'float') {
            return (float) $value;
        } elseif ($type->getName() === 'bool') {
            return (bool) $value;
        }

        return $value;
    }
}
