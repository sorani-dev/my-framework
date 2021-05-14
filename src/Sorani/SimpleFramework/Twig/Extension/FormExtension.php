<?php

// declare(strict_types=1);

namespace Sorani\SimpleFramework\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Generate HTML form elements
 */
class FormExtension extends AbstractExtension
{
    /**
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('field', [$this, 'field'], ['needs_context' => true, 'is_safe' => ['html']]),
        ];
    }

    /**
     * Create a Bootstrap 4 form field
     *
     * @param  array $context Twig Context
     * @param  string $key Field Key (id, name)
     * @param  mixed $value Field value
     * @param  string $label Field label
     * @param  array $options
     * @return string Generated form field as string
     */
    public function field(array $context, $key, $value, $label, array $options = [])
    {
        $type = isset($options['type'])  ? $options['type'] : 'text';
        $errors = isset($context['errors']) ? $context['errors'] : false;
        $class = ['mb-3'];

        $value = $this->convertValue($value);

        $attributes = [
            'class' => trim('form-control ' . (isset($options['class']) ? $options['class'] : '')),
            'name' => $key,
            'id' => $key,
        ];
        $labelClass = "";

        $errorsHtml = $this->getErrorHtml($context, $key);

        if (isset($errors[$key])) {
            $class[] = 'has-danger';
            $attributes['class'] .= ' is-invalid';
            $attributes['aria-describedby'] = $key . 'FieldFeedback';
        }

        if ($type === 'textarea') {
            $input = $this->textarea($value, $attributes);
        } elseif ($type === 'file') {
            unset($options['type'], $options['class']);
            $attributes = array_merge($attributes, $options);
            $input = $this->file($attributes);
            $labelClass =  'class="custom-file-label"';
            $class[] = 'custom-file align-middle';
        } elseif ($type === 'checkbox') {
            $class[] = 'custom-control custom-checkbox';
            return $input = $this->checkbox($value, $attributes, $label, $class);
            $labelClass =  ' class="custom-control-label"';
        } elseif ($type === 'password') {
            $input = $this->input($value, $attributes, 'password');
        } elseif (isset($options['options'])) {
            $input = $this->select($value, $options['options'], $attributes);
        } else {
            $input = $this->input($value, $attributes);
        }
        $class = join(' ', $class);

        return <<<"EOT"
        <div class="{$class}">
    <label for="{$key}"{$labelClass}>{$label}</label>
    {$input}{$errorsHtml}
</div>
EOT;
    }

    /**
     * Textarea field
     *
     * @param  string|null $value
     * @param  array $attributes
     * @return string
     */
    public function textarea($value = null, array $attributes = [])
    {
        return "<textarea " . $this->getHtmlFromArray($attributes) . " rows=\"10\">{$value}</textarea>";
    }

    /**
     * Input field (text input only)
     *
     * @param  string|null $value Field value
     * @param  array $attributes class, ...
     * @param string $type type of input (text, password)
     * @return string
     */
    public function input($value = null, array $attributes = [], $type = 'text')
    {
        return  "<input type=\"" . $type . "\" " . $this->getHtmlFromArray($attributes) . " value=\"{$value}\">";
    }

    /**
     * Input file field
     *
     * @param  array $attributes class, ...
     * @return string
     */
    public function file(array $attributes)
    {
        // $attributes['class'] .= " form-control-file";
        // $attributes['class'] =
        $attributes['class'] .= ' custom-file-input';
        return  "<input type=\"file\" " . $this->getHtmlFromArray($attributes) . ">";
    }


    /**
     * Checkbox field
     *
     * @param  string|null $value Field value
     * @param  array $attributes class, ...
     * @return string
     */
    public function checkbox($value = null, array $attributes = [], $label = '', array $class = [])
    {

        $attributes['class'] = 'custom-control-input';
        $html = '<input type="hidden" name="' . $attributes['name'] . '" value="0">';
        if ($value) {
            $attributes['checked'] = true;
        }
        $class = implode(' ', $class);
        $class = str_replace('form-control', '', $class);
        return sprintf(
            '<div class="%s">%s<input type="checkbox" %s value="1">' .
             '<label class="custom-control-label" for="%s">%s</label></div>',
            $class,
            $html,
            $this->getHtmlFromArray($attributes),
            $attributes['id'],
            $label
        );
        return  $html . "<input type=\"checkbox\" " . $this->getHtmlFromArray($attributes) . ' value="1">';
    }

    /**
     * Select field
     *
     * @param  mixed|string|null $value Field value
     * @param  array $options select options as key value pair [key1 => value1, ...]
     * @param  array $attributes class, ...
     * @return string
     */
    public function select($value, array $options, $attributes = [])
    {
        if (false !== strpos($attributes['class'], 'custom-select')) {
            $attributes['class'] = trim(str_replace('form-control', '', $attributes['class']));
        }
        $htmlOptions = array_reduce(array_keys($options), function ($html, $key) use ($options, $value) {
            $params = ['value' => $key, 'selected' =>  (string)$key === (string)$value];
            return
                $html . '<option ' . $this->getHtmlFromArray($params) . '>' . $options[$key] . '</option>';
        }, '');
        return "<select " . $this->getHtmlFromArray($attributes) . ">" . $htmlOptions . "</select>";
    }

    /**
     * getContextFieldError
     *
     * @param  array $context
     * @return string
     */
    protected function getContextFieldError(array $context)
    {
        $classes = ['form-control'];
        return $classes . ' ' . !empty($context['errors']) ? 'is-invalid' : '';
    }

    /**
     * Get errors as HTML for the text hint below the field
     *
     * @param  array $context Twig Context
     * @param  string $key Field key
     * @return string
     */
    protected function getErrorHtml(array $context, $key)
    {
        $errors = isset($context['errors']) ? $context['errors'] : false;

        if (isset($errors[$key])) {
            return '<small class="invalid-feedback" id="' . $key . 'FieldFeedback">' . $errors[$key] . '</small>';
        }
        return '';
    }

    /**
     * Join HTML field attributes (eg: class, aria-label, checked, data-*, minlength, ...)
     *
     * @param  array $attributes
     * @return string
     */
    protected function getHtmlFromArray(array $attributes)
    {
        $htmlParsed = [];
        foreach ($attributes as $key => $value) {
            if ($value === true) {
                $htmlParsed[] =  (string)$key;
            } elseif ($value !== false) {
                $htmlParsed[] =  sprintf('%s="%s"', $key, $value);
            }
        }
        return implode(' ', $htmlParsed);
    }

    /**
     * Convert a value to a string if it is an object
     * eg: \DateTimeInterface
     *
     * @param  mixed $value
     * @return mixed|null
     */
    protected function convertValue($value)
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }
        return $value;
    }
}
