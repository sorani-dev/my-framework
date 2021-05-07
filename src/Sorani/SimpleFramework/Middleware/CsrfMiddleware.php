<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sorani\SimpleFramework\Exception\CsrfInvalidException;
use Sorani\SimpleFramework\Session\SessionInterface;
use TypeError;

class CsrfMiddleware implements MiddlewareInterface
{
    /**
     * Token sent by the submitted form or equivalent
     * @var string
     */
    private $formKey = '_csrf';

    /**
     * Key stored in the session
     * @var string
     */
    private $sessionKey = 'csrf';


    /**
     * Upper limit of token
     * @var int
     */
    private $limit = 50;
    /**
     * @var \ArrayAccess
     */
    private $session;

    /**
     * CsrfMiddleware Constructor
     *
     * @param  \ArrayAccess $session
     * @param  int $limit The upper limit of number of tokens
     * @param  string $formKey The key used by the form
     * @param  string $sessionKey The key used for the CSRF in the current $session
     * @return void
     */
    public function __construct(
        \ArrayAccess &$session,
        int $limit = 50,
        string $formKey = '_csrf',
        string $sessionKey = 'csrf'
    ) {
        $this->isValidSession($session);
        $this->formKey = $formKey;
        $this->sessionKey = $sessionKey;
        $this->session = &$session;
    }

    /**
     * process
     *
     * @param  mixed $request
     * @param  mixed $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $params = $request->getParsedBody() ?? [];
            if (!isset($params[$this->formKey])) {
                $this->reject();
            } else {
                $csrfList = $this->session[$this->sessionKey] ?? [];
                if (in_array($params[$this->formKey], $csrfList)) {
                    $this->useToken($params[$this->formKey]);
                } else {
                    $this->reject();
                }
            }
        }
        return $handler->handle($request);
    }

    /**
     * Generate a random token
     *
     * @return string
     */
    public function generateToken(): string
    {
        $token = bin2hex(random_bytes(16));
        $csrfList = $this->session[$this->sessionKey] ?? [];
        $csrfList[] = $token;
        $this->session[$this->sessionKey] = $csrfList;
        $this->limitTokens();
        return $token;
    }

    /**
     * reject
     *
     * @return void
     * @throws CsrfInvalidException
     */
    private function reject(): void
    {
        throw new CsrfInvalidException();
    }

    /**
     * Remove token from list
     *
     * @param  string $currentToken
     * @return void
     */
    private function useToken(string $currentToken): void
    {
        $newTokens = array_filter($this->session[$this->sessionKey], function ($t) use ($currentToken) {
            return $currentToken !== $t;
        });
        $this->session[$this->sessionKey] = $newTokens;
    }

    /**
     * Limit the number of tokens in the session
     *
     * @return void
     */
    private function limitTokens(): void
    {
        $tokens = $this->session[$this->sessionKey] ?? [];
        if (count($tokens) > $this->limit) {
            array_shift($tokens);
        }
        $this->session[$this->sessionKey] = $tokens;
    }

    /**
     * isValidSession
     *
     * @param  array|\ArrayAccess|mixed $session
     * @return void
     * @throws \TypeError
     */
    private function isValidSession($session)
    {
        if (!is_array($session) && !$session instanceof SessionInterface && !$session instanceof \ArrayAccess) {
            throw new TypeError('The session is not an array or does not implement ArrayAccess');
        }
    }

    /**
     * Get token sent by the submitted form or equivalent
     *
     * @return  string
     */
    public function getFormKey()
    {
        return $this->formKey;
    }
}
