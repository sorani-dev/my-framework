<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MethodMiddleware implements MiddlewareInterface
{
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $parsedBody = $request->getParsedBody();

        if (
            isset($parsedBody['_method']) &&
            in_array($parsedBody['_method'], ['PATCH', 'PUT', 'DELETE'])
        ) {
            $request = $request->withMethod($parsedBody['_method']);
        }
        //  var_dump($next);
        return $next($request);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();

        if (
            isset($parsedBody['_method']) &&
            in_array($parsedBody['_method'], ['PATCH', 'PUT', 'DELETE'])
        ) {
            $request = $request->withMethod($parsedBody['_method']);
        }
        return $handler->handle($request);
    }
}
