<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Renderer;

use Sorani\SimpleFramework\Renderer\RendererInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigRenderer implements RendererInterface
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var FilesystemLoader
     */
    private $loader;

    /**
     * __construct
     *
     * @param  string $path
     * @return void
     */
    public function __construct(string $path)
    {
        $this->loader = new \Twig\Loader\FilesystemLoader($path);
        $this->twig = new \Twig\Environment($this->loader, [

        ]);
    }

    /**
     * Add namespaced path to renderer for loading views
     *
     * @param  string $namespace
     * @param  string|null $path
     * @return void
     */
    public function addPath(string $namespace, ?string $path = null): void
    {
        $this->loader->addPath($path, $namespace);
    }

    /**
     * Adds a Global variable which can then
     * be accessed by all views
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function addGlobal(string $key, $value): void
    {
        $this->twig->addGlobal($key, $value);
    }

    /**
     * Render a view
     *
     * @param  string $view view path:
     * format: @namespace/<view> for namespaced view or
     * <view> for default path
     * Path can be specified with namespace added using the addPath method
     * $this->render('@blog/view')
     * $this->render('view')
     * @param  mixed $params
     * @return string
     */
    public function render(string $view, array $params = []): string
    {
        return $this->twig->render($view . '.twig', $params);
    }

    /**
     * Check if view is namespaced
     *
     * @param  mixed $view view path
     * @return bool
     */
    private function hasNamespace(string $view): bool
    {
        return $view[0] === '@';
    }


    /**
     * Remove the namespace from the view path
     *
     * @param  string $view view path
     * @return string
     */
    private function getNamespace(string $view): string
    {
        return substr($view, 1, strpos($view, '/') - 1);
    }

    /**
     * Replace the namespace with the base path from the view path
     *
     * @param  string $view view path
     * @return string
     */
    private function replaceNamespace(string $view): string
    {
        $namespace = $this->getNamespace($view);
        return str_replace('@' . $namespace, $this->paths[$namespace], $view);
    }
}
