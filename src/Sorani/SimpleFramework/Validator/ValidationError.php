<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Validator;

/**
 * Validation error messages
 */
class ValidationError
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $rule;

    /**
     * @var array
     */
    private $attributes = [];

    /**
     * Error Messages
     * @var array
     */
    private $messages = [
        'required' => "The field %s is required",
        'slug' => "The field %s is not a valid slug",
        'notEmpty' => "The field %s must not be empty",
        'betweenLength' => "The %s field must contain between %d and %d characters",
        'minLength' => 'The %s field must contain more than %d characters',
        'maxLength' => 'The %s field must contain less than %d characters',
        'dateTime.invalid' => 'The field %s must have a valid datetime format (%)',
        'dateTime.error' => 'The field %s must be a valid datetime (%)',
        'table.exists' => "The field %s doesn't exist in the table %s",
        'table.unique' => "The field %s must be unique",
        'filetype' => "The field %s is not a valid format (%s)",
        'uploaded' => "You must upload a file",
    ];

    /**
     * ValidationError Constructor
     *
     * @param  string $key
     * @param  string $rule
     */
    public function __construct(string $key, string $rule, array $attributes = [])
    {
        $this->key = $key;
        $this->rule = $rule;
        $this->attributes = $attributes;
    }

    /**
     * Get the key
     *
     * @return  string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Get the validation rule
     *
     * @return  string
     */
    public function getRule(): string
    {
        return $this->rule;
    }

    /**
     * Get the value of attributes
     *
     * @return  array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString(): string
    {
        $params = array_merge([$this->messages[$this->rule], $this->key], $this->attributes);
        return (string)call_user_func_array('sprintf', $params);
    }
}
