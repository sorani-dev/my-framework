<?php

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

use App\Blog\BlogModule;
use App\Admin\AdminModule;
use Franzl\Middleware\Whoops\WhoopsMiddleware;

use function Http\Response\send;
use GuzzleHttp\Psr7\ServerRequest;
use Middlewares\Whoops;
use Sorani\SimpleFramework\Middleware\{
    MethodMiddleware,
    RouterMiddleware,
    NotFoundMiddleware,
    DispatcherMiddleware,
    TrailingSlashMiddleware
};

$modules = [
    AdminModule::class,
    BlogModule::class,
];



$app = (new \Sorani\SimpleFramework\App(dirname(__DIR__) . '/config/config.php'))
    ->addModule(AdminModule::class)
    ->addModule(BlogModule::class)
    ->pipe(Whoops::class)
    ->pipe(TrailingSlashMiddleware::class)
    ->pipe(MethodMiddleware::class)
    ->pipe(RouterMiddleware::class)
    ->pipe(DispatcherMiddleware::class)

    ->pipe(NotFoundMiddleware::class);

if (php_sapi_name() !== 'cli') {
    $response = $app->run(ServerRequest::fromGlobals());

    send($response);
}
