<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Twig\Extension;

use Sorani\SimpleFramework\Router;
use Twig\TwigFunction;

class RouterTwigExtension extends \Twig\Extension\AbstractExtension
{
    /**
     * @var Router
     */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('path_for', [$this, 'pathFor']),
            new TwigFunction('path', [$this, 'pathFor']),
            new TwigFunction('is_subpath', [$this, 'isSubPath']),
        ];
    }

    /**
     * Generate a path for a route by its name
     *
     * @param  string $path the route name
     * @param  array $params the route parameters if any
     * @return string
     */
    public function pathFor(string $path, ?array $params = []): string
    {
        return $this->router->generateUri($path, $params);
    }

    /**
     * Check if the current URI contains the requested Route Path
     * (eg: 'blog.show')
     *
     * @param  string $path The path to match
     * @return bool Matched path, the path is part of the REQUEST_URI
     */
    public function isSubPath(string $path): bool
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $expectedUri = $this->router->generateUri($path);
        return strpos($uri, $expectedUri) !== false;
    }
}
