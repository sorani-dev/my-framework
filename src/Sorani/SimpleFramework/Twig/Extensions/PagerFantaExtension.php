<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Twig\Extensions;

use Pagerfanta\Pagerfanta;
use Pagerfanta\View\TwitterBootstrap4View;
use Sorani\SimpleFramework\Router;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PagerFantaExtension extends AbstractExtension
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
     * @return TwigFunction
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('paginate', [$this, 'paginate'], ['is_safe' => ['html']]),
        ];
    }

    public function paginate(Pagerfanta $paginatedResults, string $routeName, array $queryArgs = []): string
    {
        $view = new TwitterBootstrap4View();
        $options  = ['proximity' => 3];
        return $view->render($paginatedResults, function (int $currentPage) use ($routeName, $queryArgs) {
            if ($currentPage > 1) {
                $queryArgs['p'] = $currentPage;
            }
            return $this->router->generateUri($routeName, [], $queryArgs);
        }, $options);
    }
}
