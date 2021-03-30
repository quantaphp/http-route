<?php

namespace Quanta\Http;

final class RouteKeyList
{
    /**
     * @var array<int, string>
     */
    private $keys;

    public function __construct(string ...$keys)
    {
        $this->keys = $keys;
    }

    public function with(string ...$keys): self
    {
        return new self(...$this->keys, ...$keys);
    }

    public function isEmpty(): bool
    {
        return count($this->keys) == 0;
    }

    public function value(string $sep): string
    {
        return implode($sep, $this->keys);
    }
}
