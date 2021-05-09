<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FormExtension extends AbstractExtension
{
    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
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
     * @return string
     */
    public function field(array $context, string $key, $value, string $label, array $options = []): string
    {
        $type = $options['type'] ?? 'text';
        $errors = $context['errors'] ?? false;
        $class = ['mb-3'];

        $value = $this->convertValue($value);

        $attributes = [
            'class' => trim('form-control ' . ($options['class'] ?? '')),
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
            $input = $this->file($attributes);
            $labelClass =  'class="custom-file-label"';
            $class[] = 'custom-file align-middle';
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
    public function textarea(?string $value = null, array $attributes): string
    {
        return "<textarea " . $this->getHtmlFromArray($attributes) . " rows=\"10\">{$value}</textarea>";
    }

    /**
     * Input field
     *
     * @param  string|null $value Field value
     * @param  array $attributes class, ...
     * @return string
     */
    public function input(?string $value = null, array $attributes): string
    {
        return  "<input type=\"text\" " . $this->getHtmlFromArray($attributes) . " value=\"{$value}\">";
    }

    /**
     * Input file field
     *
     * @param  array $attributes class, ...
     * @return string
     */
    public function file(array $attributes): string
    {
        // $attributes['class'] .= " form-control-file";
        // $attributes['class'] =
        $attributes['class'] .= ' custom-file-input';
        return  "<input type=\"file\" " . $this->getHtmlFromArray($attributes) . ">";
    }

    /**
     * Select field
     *
     * @param  mixed|string|null $value Field value
     * @param  array $options select options as key value pair [key1 => value1, ...]
     * @param  array $attributes class, ...
     * @return string
     */
    public function select($value, array $options, ?array $attributes = []): string
    {
        if (false !== strpos($attributes['class'], 'custom-select')) {
            $attributes['class'] = trim(str_replace('form-control', '', $attributes['class']));
        }
        $htmlOptions = array_reduce(array_keys($options), function (string $html, $key) use ($options, $value) {
            $params = ['value' => $key, 'selected' =>  (string)$key === (string)$value];
            return
                $html . '<option ' . $this->getHtmlFromArray($params) . '>' . $options[$key] . '</option>';
        }, '');
        return "<select " . $this->getHtmlFromArray($attributes) . ">" . $htmlOptions . "</select>";
    }

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
    protected function getErrorHtml(array $context, string $key)
    {
        $errors = $context['errors'] ?? false;

        if (isset($errors[$key])) {
            return '<small class="invalid-feedback" id="' . $key . 'FieldFeedback">' . $errors[$key] . '</small>';
            // return '<small class="form-text text-muted invalid-feedback" id="'
            // . $key . 'FieldFeedback">' . $errors[$key] . '</small>';
        }
        return '';
    }

    /**
     * Join HTML field attributes (eg: class, aria-label, checked, data-*, minlength, ...)
     *
     * @param  array $attributes
     * @return string
     */
    protected function getHtmlFromArray(array $attributes): string
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
