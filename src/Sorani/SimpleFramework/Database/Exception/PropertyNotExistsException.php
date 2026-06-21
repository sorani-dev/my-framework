<?php

namespace Sorani\Database\Exception;

/**
 * Class DbException
 *
 * @package sorani\Soratori\Database
 */
class PropertyNotExistsException extends \UnexpectedValueException
{
    protected $message = 'Property not exists';
}
