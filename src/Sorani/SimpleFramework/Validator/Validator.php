<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Validator;

use DateTimeImmutable;

class Validator
{
    /**
     * @var array
     */
    private $params;

    /**
     * @var ValidationError[]
     */
    private $errors = [];

    /**
     * Validator Contructor
     *
     * @param  array $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Check the fields are in the parameters array
     *
     * @param  string[] $keys
     * @return self
     */
    public function required(string ...$keys): self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (null === $value) {
                $this->addError($key, 'required');
            }
        }
        return $this;
    }

    /**
     * Check the field is not an empty string
     *
     * @param  string[] $keys
     * @return self
     */
    public function notEmpty(string ...$keys): self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            // if (is_string($value)) {
            //     $value = trim($value);
            // }
            if (null === $value || empty(trim($value))) {
                $this->addError($key, 'notEmpty');
            }
        }
        return $this;
    }

    /**
     * Check that the element is a slug
     *
     * @param  string $key
     * @return self
     */
    public function slug(string $key): self
    {
        $value = $this->getValue($key);

        $pattern = '/^([a-z0-9]+\-?)+$/';

        if (null !== $value && !preg_match($pattern, $value)) {
            $this->addError($key, 'slug');
        }
        return $this;
    }
    public function length(string $key, ?int $minLength = null, ?int $maxLength = null): self
    {
        $value = $this->getValue($key);
        $encoding = mb_detect_encoding($value);
        $length = mb_strlen($value, false !== $encoding ? $encoding : null);

        if (
            null !== $minLength &&
             null !== $maxLength &&
             ($length < $minLength || $length > $maxLength)
        ) {
                 $this->addError($key, 'betweenLength', [$minLength, $maxLength]);
                 return $this;
        }

        if (
            null !== $minLength &&
            $length < $minLength
        ) {
            $this->addError($key, 'minLength', [$minLength]);
            return $this;
        }

        if (
            null !== $maxLength &&
            $length > $maxLength
        ) {
            $this->addError($key, 'maxLength', [$maxLength]);
            return $this;
        }

        return $this;
    }

    /**
     * Check the field is a Full DateTime (\DateTimeInterface)
     *
     * @param  string $key
     * @param  string $format DateTime format
     *      year, month, day, hour, minutes and seconds are all required
     * @return self
     */
    public function dateTime(string $key, ?string $format = 'Y-m-d H:i:s'): self
    {
        $value = $this->getValue($key);
        if (null === $value) {
            return $this;
        }
        \DateTimeImmutable::createFromFormat($format, $value);

        $errors = \DateTimeImmutable::getLastErrors();
        if ($errors['error_count'] > 0) {
            $this->addError($key, 'dateTime.invalid');
        } elseif ($errors['warning_count'] > 0) {
            $this->addError($key, 'dateTime.error');
        }
        return $this;
    }

    /**
     * Retrieve the errors
     *
     * @return  ValidationError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Add an error
     *
     * @param  string $key
     * @param  string $rule
     * @param  array $attributes
     * @return void
     */
    public function addError(string $key, string $rule, ?array $attributes = []): void
    {
        $this->errors[$key] = new ValidationError($key, $rule, $attributes);
    }

    /**
     * Get the value of errors
     *
     * @return  ValidationError
     */
    public function getError()
    {
        return $this->errors;
    }

    /**
     * Retrieve the value of a field
     *
     * @param  string $key
     * @return mixed|null
     */
    public function getValue(string $key)
    {
        return $this->params[$key] ?? null;
    }

    /**
     * isValid
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return empty($this->errors);
    }
}
