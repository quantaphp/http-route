<?php

namespace Quanta\Http;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RouteFactory
{
    /**
     * @var array<int, MiddlewareInterface>
     */
    private array $middleware;

    public static function root(): self
    {
        return new self('', new RouteKeyList, []);
    }

    /**
     * @param array<mixed> $metadata
     */
    public function __construct(
        private string $pattern,
        private RouteKeyList $keys,
        private array $metadata,
        MiddlewareInterface ...$middleware,
    ) {
        $this->middleware = $middleware;
    }

    public function name(string ...$keys): self
    {
        return new self(
            $this->pattern,
            $this->keys->with(...$keys),
            $this->metadata,
            ...$this->middleware,
        );
    }

    public function pattern(string $pattern): self
    {
        return new self(
            $this->pattern . $pattern,
            $this->keys,
            $this->metadata,
            ...$this->middleware,
        );
    }

    public function with(int|string $key, mixed $value): self
    {
        return new self(
            $this->pattern,
            $this->keys,
            array_merge($this->metadata, [$key => $value]),
            ...$this->middleware,
        );
    }

    public function middleware(MiddlewareInterface|callable ...$middleware): self
    {
        return new self (
            $this->pattern,
            $this->keys,
            $this->metadata,
            ...$this->middleware,
            ...array_map([$this, 'wrapMiddleware'], $middleware),
        );
    }

    public function get(RequestHandlerInterface|callable $handler): Route
    {
        return $this->route(MethodList::get(), $handler);
    }

    public function post(RequestHandlerInterface|callable $handler): Route
    {
        return $this->route(MethodList::post(), $handler);
    }

    public function put(RequestHandlerInterface|callable $handler): Route
    {
        return $this->route(MethodList::put(), $handler);
    }

    public function delete(RequestHandlerInterface|callable $handler): Route
    {
        return $this->route(MethodList::delete(), $handler);
    }

    public function patch(RequestHandlerInterface|callable $handler): Route
    {
        return $this->route(MethodList::patch(), $handler);
    }

    public function head(RequestHandlerInterface|callable $handler): Route
    {
        return $this->route(MethodList::head(), $handler);
    }

    public function options(RequestHandlerInterface|callable $handler): Route
    {
        return $this->route(MethodList::options(), $handler);
    }

    public function all(RequestHandlerInterface|callable $handler): Route
    {
        return $this->route(MethodList::all(), $handler);
    }

    public function route(MethodList $methods, RequestHandlerInterface|callable $handler): Route
    {
        return new Route(
            $methods,
            $this->pattern,
            $this->keys,
            $this->metadata,
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
