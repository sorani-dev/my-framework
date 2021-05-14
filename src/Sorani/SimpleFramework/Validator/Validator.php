<?php

// declare(strict_types=1);

namespace Sorani\SimpleFramework\Validator;

use Psr\Http\Message\UploadedFileInterface;
use Sorani\SimpleFramework\Database\Table;

class Validator
{
    /**
     * Authorized MIME extensions with their corresponding file extensions
     * @var array
     */
    const MIME_TYPES = [
        'jpg' =>  'image/jpeg',
        'jpeg' => 'image/jpeg',
        'pdf' => 'application/pdf',
        'png' => 'image/png',
    ];

    /**
     * Input parameters
     * @var array
     */
    private $params;

    /**
     * List of validation errors
     *
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
     * @param  string[] $keys Fields names
     * @return self
     */
    public function required(...$keys)
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
     * @param  string[] $keys Fields names
     * @return self
     */
    public function notEmpty(...$keys)
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (null === $value || empty(trim($value))) {
                $this->addError($key, 'notEmpty');
            }
        }
        return $this;
    }

    /**
     * Check that the element is a slug
     *
     * @param  string $key Field name
     * @return self
     */
    public function slug($key)
    {
        $value = $this->getValue($key);

        $pattern = '/^[^-]([a-z0-9]+\-?)+[^-]$/';
        // $pattern = '/^[a-z0-9]+(-[a-z0-9]+)*$/';

        if (null !== $value && !preg_match($pattern, $value)) {
            $this->addError($key, 'slug');
        }
        return $this;
    }

    /**
     * Length is less than or greater than a value
     *
     * @param  string $key Field name
     * @param  int|null $minLength minimum length or no minimum if null
     * @param  int|null $maxLength maximum lengthor no maximum if null
     * @return self
     */
    public function length($key, $minLength = null, $maxLength = null)
    {
        $value = $this->getValue($key);
        $encoding = mb_detect_encoding($value);
        $length = mb_strlen($value, false !== $encoding ? $encoding : null);

        if (null !== $minLength &&
            null !== $maxLength &&
            ($length < $minLength || $length > $maxLength)
        ) {
            $this->addError($key, 'betweenLength', [$minLength, $maxLength]);
            return $this;
        }

        if (null !== $minLength &&
            $length < $minLength
        ) {
            $this->addError($key, 'minLength', [$minLength]);
            return $this;
        }

        if (null !== $maxLength &&
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
     * @param  string $key Field name
     * @param  string $format DateTime format
     *      year, month, day, hour, minutes and seconds are all required
     * @return self
     */
    public function dateTime($key, $format = 'Y-m-d H:i:s')
    {
        $value = $this->getValue($key);
        if (null === $value) {
            return $this;
        }
        \DateTimeImmutable::createFromFormat($format, $value);

        $errors = \DateTimeImmutable::getLastErrors();
        if ($errors['error_count'] > 0) {
            $this->addError($key, 'dateTime.invalid', ['format' => $format]);
        } elseif ($errors['warning_count'] > 0) {
            $this->addError($key, 'dateTime.error', ['format' => $format]);
        }
        return $this;
    }

    /**
     * Check that the element exists in the table
     *
     * @param  key $key Field name
     * @param  Table $table Table
     * @return self
     */
    public function existsKey($key, Table $table)
    {
        if (!$table->exists((int)$key)) {
            $this->addError($key, 'table.exists', [$table->getTable()]);
        }
        return $this;
    }

    /**
     * Check that the element exists in the table
     *
     * @param  key $key Field name
     * @param  Table $table Table
     * @param \PDO $pdo
     * @return self
     */
    public function existsRecord($key, $table, \PDO $pdo)
    {
        $value = $this->getValue($key);
        $statement = $pdo->prepare("SELECT id FROM {$table} WHERE id = ?");
        $statement->execute([$value]);

        if ($statement->fetchColumn() === false) {
            $this->addError($key, 'table.exists', [$table]);
        }
        return $this;
    }

    /**
     * Check that the element is unique in the table
     *
     * @param  key $key Field name
     * @param  string $table Table name to check
     * @param \PDO $pdo
     * @param int|null $exclude excluded primary key values
     * @return self
     */
    public function uniqueRecord($key, $table, \PDO $pdo, $exclude = null)
    {
        $value = $this->getValue($key);
        $query = "SELECT id FROM {$table} WHERE $key = ?";
        $params = [$value];
        if (null !== $exclude) {
            $query .= ' AND id != ?';
            $params[] = $exclude;
        }

        $statement = $pdo->prepare($query);
        $statement->execute($params);

        if ($statement->fetchColumn() !== false) {
            $this->addError($key, 'table.unique', [$value]);
        }
        return $this;
    }

    /**
     * Check if the file has been uploaded successfully
     *
     * @param  string $key Field name
     * @return self
     */
    public function uploaded($key)
    {
        /** @var UploadedFileInterface $file */
        $file = $this->getValue($key);
        if (null === $file || $file->getError() !== UPLOAD_ERR_OK) {
            $this->addError($key, 'uploaded');
        }
        return $this;
    }

    /**
     * Checks the file extenstion(s)
     *
     * @param  string $key Field name
     * @param  string[] $extensions Accepted extensions
     * @return self
     */
    public function extension($key, array $extensions)
    {
        /** @var UploadedFileInterface */
        $file = $this->getValue($key);
        // if file exists and the upload was OK
        if (null !== $file && UPLOAD_ERR_OK === $file->getError()) {
            $type = $file->getClientMediaType();
            $extension = mb_strtolower(pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));
            $expectedMimeType = !empty(self::MIME_TYPES[$extension]) ? self::MIME_TYPES[$extension] : null;

            if (!in_array($extension, $extensions) || $expectedMimeType !== $type) {
                $this->addError($key, 'filetype', [implode(',', $extensions)]);
            }
        }

        return $this;
    }

    /**
     * Check if the field is a boolean value
     *
     * @param  string $key Field name
     * @param  bool $strict Strict comparaison: true or false.
     *  If not strict, truthy or falsy values (true, 1, false, 0, ...)
     * @return self
     */
    public function boolean($key, $strict = false)
    {
        $value = $this->getValue($key);

        if ($strict === true) {
            if ($value !== true && $value !== false) {
                $this->addError($key, 'boolean');
            }
        } elseif ($value !== true && $value !== false
            && $value !== 1 && $value !== 0 && $value !== '0' && $value !== '1'
        ) {
            $this->addError($key, 'boolean');
        }
        return $this;
    }

    /**
     * Retrieve the errors
     *
     * @return  ValidationError[]
     */
    public function getErrors()
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
    public function addError($key, $rule, array $attributes = [])
    {
        $this->errors[$key] = new ValidationError($key, $rule, $attributes);
    }

    /**
     * Get the ValidationError of a field
     *
     * @param string $key
     * @return  ValidationError|null
     */
    public function getError($key)
    {
        return isset($this->errors[$key]) ? $this->errors[$key] : null;
    }

    /**
     * Retrieve the value of a field
     *
     * @param  string $key Field name
     * @return mixed|null
     */
    public function getValue($key)
    {
        return isset($this->params[$key]) ? $this->params[$key] : null;
    }

    /**
     * Is the Validator valid
     *
     * @return bool no errors
     */
    public function isValid()
    {
        return empty($this->errors);
    }
}
