<?php

// declare(strict_types=1);

namespace App\Auth\Twig\Extension;

use Sorani\SimpleFramework\Auth\AuthInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AuthTwigExtension extends AbstractExtension
{
    /**
     * @var AuthInterface
     */
    private $auth;

    /**
     * Constructor
     *
     * @param  AuthInterface $auth
     */
    public function __construct(AuthInterface $auth)
    {
        $this->auth = $auth;
    }
    /**
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('current_user', [$this->auth, 'getUser']),
        ];
    }
}
