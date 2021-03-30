<?php

namespace Quanta\Http;

use Psr\Http\Server\MiddlewareInterface;

final class RouteGroup
{
    /**
     * @var array<int, \Psr\Http\Server\MiddlewareInterface>
     */
    private array $middleware;

    public function __construct(
        private string|null $namePrefix = null,
        private string $patternPrefix = '',
        MiddlewareInterface ...$middleware,
    ) {
        $this->middleware = $middleware;
    }

    public function named(string $name): self
    {
        $prefixedName = !is_null($this->namePrefix)
            ? $this->namePrefix . '.' . $name
            : $name;

        return new self($prefixedName, $this->patternPrefix, ...$this->middleware);
    }

    public function group(string $pattern): self
    {
        $prefixedPattern = $this->patternPrefix . $pattern;

        return new self($this->namePrefix, $prefixedPattern, ...$this->middleware);
    }

    public function get(string $pattern): RouteFactory
    {
        return $this->any(Methods::get(), $pattern);
    }

    public function post(string $pattern): RouteFactory
    {
        return $this->any(Methods::post(), $pattern);
    }

    public function put(string $pattern): RouteFactory
    {
        return $this->any(Methods::put(), $pattern);
    }

    public function delete(string $pattern): RouteFactory
    {
        return $this->any(Methods::delete(), $pattern);
    }

    public function patch(string $pattern): RouteFactory
    {
        return $this->any(Methods::patch(), $pattern);
    }

    public function head(string $pattern): RouteFactory
    {
        return $this->any(Methods::head(), $pattern);
    }

    public function options(string $pattern): RouteFactory
    {
        return $this->any(Methods::options(), $pattern);
    }

    public function all(string $pattern): RouteFactory
    {
        return $this->any(Methods::all(), $pattern);
    }

    public function any(Methods $methods, string $pattern): RouteFactory
    {
        return new RouteFactory(
            $this->namePrefix,
            $methods,
            $this->patternPrefix . $pattern,
            ...$this->middleware,
        );
    }

    public function middleware(MiddlewareInterface|callable ...$middleware): self
    {
        return new self (
            $this->namePrefix,
            $this->patternPrefix,
            ...$this->middleware,
            ...array_map([$this, 'wrapMiddleware'], $middleware),
        );
    }

    private function wrapMiddleware(MiddlewareInterface|callable $middleware): MiddlewareInterface
    {
        return is_callable($middleware)
            ? new LazyMiddleware($middleware)
            : $middleware;
    }
}
