<?php

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

use App\Blog\BlogModule;
use DI\ContainerBuilder;

use function Http\Response\send;
use GuzzleHttp\Psr7\ServerRequest;
use Sorani\SimpleFramework\Renderer\RendererInterface;

$modules = [
    BlogModule::class
];

$builder = new ContainerBuilder();

$builder->addDefinitions(require dirname(__DIR__) . '/config/config.php');

foreach ($modules as $module) {
    if ($module::DEFINITIONS) {
        $builder->addDefinitions($module::DEFINITIONS);
    }
}
$builder->addDefinitions(require dirname(__DIR__) . '/config.php');

$container = $builder->build();


$app = new \Sorani\SimpleFramework\App(
    $container,
    $modules
);
if (php_sapi_name() !== 'cli') {
    $response = $app->run(ServerRequest::fromGlobals());

    send($response);
}
