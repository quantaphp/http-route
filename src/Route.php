<?php

namespace Quanta\Http;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class Route
{
    /**
     * @var array<int, \Psr\Http\Server\MiddlewareInterface>
     */
    private array $middleware;

    public static function named(string $name): RouteGroup
    {
        return new RouteGroup($name);
    }

    public static function group(string $pattern): RouteGroup
    {
        return new RouteGroup(null, $pattern);
    }

    public static function get(string $pattern): RouteFactory
    {
        return self::any(Methods::get(), $pattern);
    }

    public static function post(string $pattern): RouteFactory
    {
        return self::any(Methods::post(), $pattern);
    }

    public static function put(string $pattern): RouteFactory
    {
        return self::any(Methods::put(), $pattern);
    }

    public static function delete(string $pattern): RouteFactory
    {
        return self::any(Methods::delete(), $pattern);
    }

    public static function patch(string $pattern): RouteFactory
    {
        return self::any(Methods::patch(), $pattern);
    }

    public static function head(string $pattern): RouteFactory
    {
        return self::any(Methods::head(), $pattern);
    }

    public static function options(string $pattern): RouteFactory
    {
        return self::any(Methods::options(), $pattern);
    }

    public static function all(string $pattern): RouteFactory
    {
        return self::any(Methods::all(), $pattern);
    }

    public static function any(Methods $methods, string $pattern): RouteFactory
    {
        return new RouteFactory(null, $methods, $pattern);
    }

    public function __construct(
        private string|null $name,
        private Methods $methods,
        private string $pattern,
        private RequestHandlerInterface $handler,
        MiddlewareInterface ...$middleware,
    ) {
        $this->middleware = $middleware;
    }

    public function hasName(): bool
    {
        return !is_null($this->name);
    }

    public function name(): string
    {
        if (is_null($this->name)) {
            throw new \LogicException(
                vsprintf('Route [%s] %s has no name', [
                    implode(', ', $this->methods->values()),
                    $this->pattern,
                ]),
            );
        }

        return $this->name;
    }

    /**
     * @return array<int, string>
     */
    public function methods(): array
    {
        return $this->methods->values();
    }

    public function pattern(): string
    {
        return $this->pattern;
    }

    public function middleware(MiddlewareInterface|callable ...$middleware): self
    {
        return new self (
            $this->name,
            $this->methods,
            $this->pattern,
            $this->handler,
            ...$this->middleware,
            ...array_map([$this, 'wrapMiddleware'], $middleware),
        );
    }

    public function handler(): RequestHandlerInterface
    {
        return queue($this->handler, ...$this->middleware);
    }

    private function wrapMiddleware(MiddlewareInterface|callable $middleware): MiddlewareInterface
    {
        return is_callable($middleware)
            ? new LazyMiddleware($middleware)
            : $middleware;
    }
}
