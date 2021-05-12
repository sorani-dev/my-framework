<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Renderer;

use Twig\Extension\DebugExtension;
use Psr\Container\ContainerInterface;
use Sorani\SimpleFramework\Renderer\TwigRenderer;

/**
 * Create a Twig Environement instance an populate it with default values
 */
class TwigRendererFactory
{

    /**
     * Invoke method
     *
     * @param  ContainerInterface $container
     * @return TwigRenderer
     */
    public function __invoke(ContainerInterface $container): TwigRenderer
    {
        // ge tthe environement type
        $debug = $container->get('env') !== 'production';

        // get path to the views
        $viewPath = $container->get('views.path');

        // intanciate the Loader and the Environement
        $loader = new \Twig\Loader\FilesystemLoader($viewPath);
        $twig = new \Twig\Environment($loader, [
            'debug' => $debug,
            'cache' => $debug ? false : 'tmp/views',
            'auto_reload' => $debug,
        ]);

        $twig->addExtension(new DebugExtension());

        // add extensions
        if ($container->has('twig.extensions')) {
            foreach ($container->get('twig.extensions') as $extension) {
                $twig->addExtension($extension);
            }
        }

        // create the renderer
        return new TwigRenderer($twig);
    }
}
