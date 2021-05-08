<?php

use Middlewares\Whoops;
use App\Blog\BlogModule;
use App\Admin\AdminModule;
use GuzzleHttp\Psr7\ServerRequest;
use Sorani\SimpleFramework\Middleware\{
    CsrfMiddleware,
    MethodMiddleware,
    RouterMiddleware,
    NotFoundMiddleware,
    DispatcherMiddleware,
    TrailingSlashMiddleware
};

use function Http\Response\send;

chdir(dirname(__DIR__));

require 'vendor' . DIRECTORY_SEPARATOR . '/autoload.php';


$modules = [
    AdminModule::class,
    BlogModule::class,
];



$app = (new \Sorani\SimpleFramework\App('config/config.php'))
    ->addModule(AdminModule::class)
    ->addModule(BlogModule::class)
    ->pipe(Whoops::class)
    ->pipe(TrailingSlashMiddleware::class)
    ->pipe(MethodMiddleware::class)
    ->pipe(CsrfMiddleware::class)
    ->pipe(RouterMiddleware::class)
    ->pipe(DispatcherMiddleware::class)

    ->pipe(NotFoundMiddleware::class);

if (php_sapi_name() !== 'cli') {
    $response = $app->run(ServerRequest::fromGlobals());

    send($response);
}
