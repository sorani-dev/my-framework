<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Database\Query;

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
            $instance = new $object();
        } else {
            $instance = $object;
        }
        foreach ($values as $key => $value) {
            $method = $this->getSetter($key);
            $property = lcfirst($this->getProperty($key));

            if (method_exists($instance, $method)) {
                $instance->$method($value);
            } elseif (property_exists($instance, $property)) {
                $instance->$property = $value;
            } else {
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
     * @return string
     */
    public function getProperty(string $fieldName): string
    {
        return implode('', array_map('ucfirst', explode('_', $fieldName)));
    }
}
