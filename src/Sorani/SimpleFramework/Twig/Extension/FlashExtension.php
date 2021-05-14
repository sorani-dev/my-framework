<?php

// declare(strict_types=1);

namespace Sorani\SimpleFramework\Twig\Extension;

use Sorani\SimpleFramework\Session\FlashService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FlashExtension extends AbstractExtension
{
    /**
     *
     * @var FlashService
     */
    private $flash;

    /**
     * FlashExtension Contructor
     *
     * @param FlashService $flash
     */
    public function __construct(FlashService $flash)
    {
        $this->flash = $flash;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [new TwigFunction('flash', [$this, 'getFlash'])];
    }

    /**
     * Get the Flash message by its type. ex: success, error
     *
     * @param    string $type Message type
     * @return   string|null
     */
    public function getFlash($type)
    {
        return $this->flash->get($type);
    }
}
