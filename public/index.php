<?php

use Middlewares\Whoops;
use App\Blog\BlogModule;
use App\Admin\AdminModule;
use App\Auth\AuthModule;
use App\Auth\ForbiddenMiddleware;
use App\Site\SiteModule;
use GuzzleHttp\Psr7\ServerRequest;
use Sorani\SimpleFramework\Middleware\{
    CsrfMiddleware,
    MethodMiddleware,
    RouterMiddleware,
    NotFoundMiddleware,
    DispatcherMiddleware,
    LoggedInMiddleware,
    TrailingSlashMiddleware
};

use function Http\Response\send;

chdir(dirname(__DIR__));

require 'vendor' . DIRECTORY_SEPARATOR . '/autoload.php';

$app = (new \Sorani\SimpleFramework\App('config/config.php'))
    ->addModule(AdminModule::class)
    ->addModule(BlogModule::class)
    ->addModule(AuthModule::class)
    ->addModule(SiteModule::class);

$container = $app->getContainer();

$app->pipe(Whoops::class)
    ->pipe(TrailingSlashMiddleware::class)
    ->pipe(ForbiddenMiddleware::class)
    ->pipe($container->get('admin.prefix'), LoggedInMiddleware::class)
    ->pipe(MethodMiddleware::class)
    ->pipe(CsrfMiddleware::class)
    ->pipe(RouterMiddleware::class)
    ->pipe(DispatcherMiddleware::class)

    ->pipe(NotFoundMiddleware::class);

if (php_sapi_name() !== 'cli') {
    $response = $app->run(ServerRequest::fromGlobals());

    send($response);
}
