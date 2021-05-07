<?php

namespace Sorani\SimpleFramework\Renderer;

use Twig\Extension\DebugExtension;
use Psr\Container\ContainerInterface;
use Sorani\SimpleFramework\Renderer\TwigRenderer;

class TwigRendererFactory
{
    public function __invoke(ContainerInterface $container): TwigRenderer
    {
        // get path to the views
        $viewPath = $container->get('views.path');

        // intanciate the Loader and the Environement
        $loader = new \Twig\Loader\FilesystemLoader($viewPath);
        $twig = new \Twig\Environment($loader, [
            'debug' => true
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
