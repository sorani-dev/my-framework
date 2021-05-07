<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Twig\Extension;

use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Several extension for Text manipulation (filters, ...)
 */
class TextExtension extends AbstractExtension
{

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new  TwigFilter('excerpt', [$this, 'excerpt'], ['is_safe' => ['html']]),
            new TwigFilter('truncate', [$this, 'truncateFilter'], ['needs_environment' => true]),
        ];
    }

    /**
     * Returns an excerpt of the content
     *
     * @param  string $content
     * @param  int $maxlength
     * @return string
     */
    public function excerpt(string $content, int $maxlength = 100): string
    {
        if (null === $content) {
            return '';
        }

        if (mb_strlen($content) > $maxlength) {
            $excerpt = mb_substr($content, 0, $maxlength);
            $lastSpace = mb_strrpos($excerpt, ' ');
            return mb_substr($excerpt, 0, $lastSpace) . '...';
        }
        return $content;
    }

    /**
     * Returns an excerpt of the content usig the env charset
     * and preserving whole words or cutting the text at the length provided
     *
     * Use the truncate filter to cut off a string after limit is reached
     * {{ "Hello World!"|truncate(5) }}
     * The example would output Hello..., as ... is the default separator.
     * You can also tell truncate to preserve whole words by setting the second parameter to true.
     * If the last Word is on the separator, truncate will print out the whole Word.
     * {{ "Hello World!"|truncate(7, true) }}
     * Here Hello World! would be printed.
     * If you want to change the separator, just set the third parameter to your desired separator.
     * {{ "Hello World!"|truncate(7, false, "??") }}
     * This example would print Hello W??.
     * from Twig Extensions
     */
    public function truncateFilter(Environment $env, $value, $length = 30, $preserve = false, $separator = '...')
    {
        if (mb_strlen($value, $env->getCharset()) > $length) {
            if ($preserve) {
                // If breakpoint is on the last word, return the value without separator.
                if (false === ($breakpoint = mb_strpos($value, ' ', $length, $env->getCharset()))) {
                    return $value;
                }

                $length = $breakpoint;
            }

            return rtrim(mb_substr($value, 0, $length, $env->getCharset())) . $separator;
        }

        return $value;
    }
}
