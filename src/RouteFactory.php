<?php

namespace Quanta\Http;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function Quanta\Http\queue;

final class RouteFactory
{
    /**
     * @var array<int, \Psr\Http\Server\MiddlewareInterface>
     */
    private array $middleware;

    public function __construct(
        private string|null $name,
        private Methods $methods,
        private string $pattern,
        MiddlewareInterface ...$middleware,
    ) {
        $this->middleware = $middleware;
    }

    public function middleware(MiddlewareInterface|callable ...$middleware): self
    {
        return new self (
            $this->name,
            $this->methods,
            $this->pattern,
            ...$this->middleware,
            ...array_map([$this, 'wrapMiddleware'], $middleware),
        );
    }

    public function handler(RequestHandlerInterface|callable $handler): Route
    {
        return new Route(
            $this->name,
            $this->methods,
            $this->pattern,
            is_callable($handler) ? new LazyRequestHandler($handler) : $handler,
            ...$this->middleware,
        );
    }

    private function wrapMiddleware(MiddlewareInterface|callable $middleware): MiddlewareInterface
    {
        return is_callable($middleware)
            ? new LazyMiddleware($middleware)
            : $middleware;
    }
}
