<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Response;

use GuzzleHttp\Psr7\Response;

class RedirectResponse extends Response
{
    public function __construct(string $url)
    {
        parent::__construct(200, ['Location' => $url]);
    }
}
