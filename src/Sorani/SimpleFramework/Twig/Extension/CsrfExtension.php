<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Twig\Extension;

use Sorani\SimpleFramework\Middleware\CsrfMiddleware;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CsrfExtension extends AbstractExtension
{

    /**
     * @var CsrfMiddleware
     */
    private $csrfMiddleware;

    public function __construct(CsrfMiddleware $csrfMiddleware)
    {
        $this->csrfMiddleware = $csrfMiddleware;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('csrf_input', [$this, 'csrfInput'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Create a CSRF hiddeen input field
     *
     * @return string
     */
    public function csrfInput(): string
    {
        return sprintf(
            '<input type="hidden" name="%s" value="%s" />',
            $this->csrfMiddleware->getFormKey(),
            $this->csrfMiddleware->generateToken()
        );
    }
}
