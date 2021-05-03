<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Renderer;

class PHPRenderer implements RendererInterface
{
    private const DEFAULT_NAMESPACE = '__MAIN__';

    /**
     * View base path list
     * @var string[]
     */
    private array $paths = [];

    /**
     * Variables which can be accessed by all views
     * @var array
     */
    private array $globals = [];

    /**
     * __construct
     *
     * @param  string|null $defaultPath
     * @return void
     */
    public function __construct(?string $defaultPath = null)
    {
        if (null !== $defaultPath) {
            $this->addPath($defaultPath);
        }
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
        if (null === $path) {
            $this->paths[self::DEFAULT_NAMESPACE] = $namespace;
        } else {
            $this->paths[$namespace] = $path;
        }
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
        $this->globals[$key] = $value;
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
        if ($this->hasNamespace($view)) {
            $path = $this->replaceNamespace($view) .  '.php';
        } else {
            $path = $this->paths[self::DEFAULT_NAMESPACE] . DIRECTORY_SEPARATOR . $view . '.php';
        }

        ob_start();
        $renderer = $this;
        extract($this->globals);
        extract($params);
        require($path);
        return ob_get_clean();
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
