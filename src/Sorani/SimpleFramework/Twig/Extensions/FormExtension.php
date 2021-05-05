<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Twig\Extensions;

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

        $errorsHtml = $this->getErrorHtml($context, $key);

        if (isset($errors[$key])) {
            $class[] = 'has-danger';
            $attributes['class'] .= ' is-invalid';
            $attributes['aria-describedby'] = $key . 'FieldFeedback';
        }

        if ($type === 'textarea') {
            $input = $this->textarea($value, $attributes);
        } else {
            $input = $this->input($value, $attributes);
        }
        $class = join(' ', $class);

        return <<<"EOT"
        <div class="{$class}">
    <label for="{$key}">{$label}</label>
    {$input}{$errorsHtml}
</div>
EOT;
    }

    /**
     * Textarea field
     *
     * @param  string $key
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
     * @param  string $key
     * @param  string|null $value Field value
     * @param  string $attributes class, ...
     * @return string
     */
    public function input(?string $value = null, array $attributes): string
    {
        return  "<input type=\"text\" " . $this->getHtmlFromArray($attributes) . " value=\"{$value}\">";
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
        return implode(' ', array_map(function ($key, $value) {
            return sprintf('%s="%s"', $key, $value);
        }, array_keys($attributes), $attributes));
    }

    /**
     * Convert a value to az astring if it an object
     * eg: \DateTimeInterface
     *
     * @param  mixed $value
     * @return string|null
     */
    protected function convertValue($value): ?string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }
        return $value;
    }
}
