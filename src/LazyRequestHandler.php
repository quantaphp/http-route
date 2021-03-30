<?php

declare(strict_types=1);

namespace Quanta\Http;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class LazyRequestHandler implements RequestHandlerInterface
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
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $handler = ($this->factory)();

        if ($handler instanceof RequestHandlerInterface) {
            return $handler->handle($request);
        }

        throw new \UnexpectedValueException(
            vsprintf('%s::{closure}(): Return value must be of type %s, %s returned', [
                self::class,
                RequestHandlerInterface::class,
                gettype($handler),
            ]),
        );
    }
}
