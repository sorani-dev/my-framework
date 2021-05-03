<?php

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

use App\Blog\BlogModule;
use function Http\Response\send;
use GuzzleHttp\Psr7\ServerRequest;

use Sorani\SimpleFramework\Renderer;

$renderer = new Renderer();
$renderer->addPath(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'resources/views');




$app = new \Sorani\SimpleFramework\App(
    [
        BlogModule::class
    ],
    [
        'renderer' => $renderer,
    ]
);

$response = $app->run(ServerRequest::fromGlobals());

send($response);
