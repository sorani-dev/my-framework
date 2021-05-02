<?php

use GuzzleHttp\Psr7\ServerRequest;

use function Http\Response\send;

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor/autoload.php';


$app = new \Sorani\SimpleFramework\App([]);

$response = $app->run(ServerRequest::fromGlobals());

send($response);
