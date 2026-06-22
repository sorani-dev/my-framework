<?php

namespace Sorani\SimpleFramework\Database\Exception;

/**
 * Class DbException
 *
 * @package sorani\database
 */
class PropertyNotExistsException extends \UnexpectedValueException
{
    protected $message = 'Property not exists';
}
