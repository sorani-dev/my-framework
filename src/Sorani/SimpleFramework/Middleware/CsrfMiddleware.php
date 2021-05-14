<?php

// declare(strict_types=1);

namespace Sorani\SimpleFramework\Middleware;

use Middlewares\Utils\RequestHandler;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sorani\SimpleFramework\Exception\CsrfInvalidException;
use Sorani\SimpleFramework\Session\SessionInterface;
use TypeError;

/**
 * CSRF for forms
 */
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
     * Current Session
     *
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
     */
    public function __construct(
        \ArrayAccess &$session,
        $limit = 50,
        $formKey = '_csrf',
        $sessionKey = 'csrf'
    ) {
        $this->isValidSession($session);
        $this->formKey = $formKey;
        $this->sessionKey = $sessionKey;
        $this->session = &$session;
        $this->limit = $limit;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler)
    {
        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $params = $request->getParsedBody();
            $params = isset($params) ? $request->getParsedBody() : [];
            if (!isset($params[$this->formKey])) {
                $this->reject();
            } else {
                $csrfList = isset($this->session[$this->sessionKey]) ? $this->session[$this->sessionKey] : [];
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
    public function generateToken()
    {
        $token = bin2hex(random_bytes(16));
        $csrfList = isset($this->session[$this->sessionKey]) ? $this->session[$this->sessionKey] : [];
        $csrfList[] = $token;
        $this->session[$this->sessionKey] = $csrfList;
        $this->limitTokens();
        return $token;
    }

    /**
     * Reject an input request
     *
     * @return void
     * @throws CsrfInvalidException
     */
    private function reject()
    {
        throw new CsrfInvalidException();
    }

    /**
     * Remove a token from list
     *
     * @param  string $currentToken
     * @return void
     */
    private function useToken($currentToken)
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
    private function limitTokens()
    {
        $tokens = isset($this->session[$this->sessionKey]) ? $this->session[$this->sessionKey] : [];
        if (count($tokens) > $this->limit) {
            array_shift($tokens);
        }
        $this->session[$this->sessionKey] = $tokens;
    }

    /**
     * Check if the session is a valid type
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
