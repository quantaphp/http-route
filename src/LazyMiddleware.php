<?php

declare(strict_types=1);

namespace Quanta\Http;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class LazyMiddleware implements MiddlewareInterface
{
    /**
     * @var callable
     */
    private $factory;

    /**
     * @param callable $factory
     */
    public function __construct(callable $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @inheritdoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $middleware = ($this->factory)();

        if ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $handler);
        }

        throw new \UnexpectedValueException(
            vsprintf('%s::{closure}(): Return value must be of type %s, %s returned', [
                self::class,
                MiddlewareInterface::class,
                gettype($middleware),
            ]),
        );
    }
}
