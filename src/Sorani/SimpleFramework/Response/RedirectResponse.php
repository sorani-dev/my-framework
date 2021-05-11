<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Response;

use GuzzleHttp\Psr7\Response;

class RedirectResponse extends Response
{
    /**
     * RedirectResponse Constructor
     *
     * @param  string $url
     * @param  int $statusCode default to 301 Moved Permanately
     */
    public function __construct(string $url, int $statusCode = 301)
    {
        parent::__construct($statusCode, ['Location' => $url]);
        // parent::__construct(200, ['Location' => $url]);
    }
}
