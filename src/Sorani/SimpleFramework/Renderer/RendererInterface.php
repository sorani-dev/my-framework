<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Renderer;

/**
 * Interface for all Renderers
 */
interface RendererInterface
{

    /**
     * Add namespaced path to renderer for loading views
     *
     * @param  string $namespace
     * @param  string|null $path
     * @return void
     */
    public function addPath(string $namespace, ?string $path = null): void;

    /**
     * Adds a Global variable which can then
     * be accessed by all views
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function addGlobal(string $key, $value): void;

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
    public function render(string $view, array $params = []): string;
}
