<?php

declare(strict_types=1);

namespace App\Blog\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DemoExtension extends AbstractExtension
{
    /**
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('demo', [$this, 'demo']),
        ];
    }

    public function demo()
    {
        return 'Hi there!';
    }
}
