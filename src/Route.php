<?php

namespace Quanta\Http;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class Route
{
    /**
     * @var array<int, MiddlewareInterface>
     */
    private array $middleware;

    /**
     * @param array<mixed> $metadata
     */
    public function __construct(
        private MethodList $methods,
        private string $pattern,
        private RouteKeyList $keys,
        private array $metadata,
        private RequestHandlerInterface $handler,
        MiddlewareInterface ...$middleware,
    ) {
        $this->middleware = $middleware;
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

    public function hasName(): bool
    {
        return !$this->keys->isEmpty();
    }

    public function name(string $sep = '.'): string
    {
        if ($this->keys->isEmpty()) {
            throw new \LogicException(
                vsprintf('Route [%s] %s has no name', [
                    implode(', ', $this->methods->values()),
                    $this->pattern,
                ]),
            );
        }

        return $this->keys->value($sep);
    }

    public function has(int|string $key): bool
    {
        return array_key_exists($key, $this->metadata);
    }

    public function get(int|string $key, mixed $default = null): mixed
    {
        return array_key_exists($key, $this->metadata) ? $this->metadata[$key] : $default;
    }

    public function handler(): RequestHandlerInterface
    {
        return queue($this->handler, ...$this->middleware);
    }
}
