<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Twig\Extensions;

use DateTimeInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TimeExtension extends AbstractExtension
{
    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('ago', [$this, 'ago'], ['is_safe' => ['html']]),
        ];
    }

    public function ago(DateTimeInterface $date, string $format = 'd/m/Y H:i'): string
    {
        return
            sprintf(
                '<time class="need_to_be_rendered" datetime="%s">%s</time>',
                $date->format(DateTimeInterface::ISO8601),
                $date->format($format)
            );
    }
}
