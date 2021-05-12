<?php

declare(strict_types=1);

namespace App\Admin;

/**
 * Interface to create Widgets for display on the Admin Dashboard
 */
interface AdminWidgetInterface
{

    /**
     * Render the Widget
     *
     * @return string
     */
    public function render(): string;

    /**
     * Inject an element in the Menu
     *
     * @return string
     */
    public function renderMenu(): string;
}
