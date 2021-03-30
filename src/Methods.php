<?php

namespace Quanta\Http;

final class Methods
{
    /**
     * @var array<int, string>
     */
    private array $values;

    public static function get(): self
    {
        return new self('GET');
    }

    public static function post(): self
    {
        return new self('POST');
    }

    public static function put(): self
    {
        return new self('PUT');
    }

    public static function delete(): self
    {
        return new self('DELETE');
    }

    public static function patch(): self
    {
        return new self('PATCH');
    }

    public static function head(): self
    {
        return new self('HEAD');
    }

    public static function options(): self
    {
        return new self('OPTIONS');
    }

    public static function all(): self
    {
        return new self('GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS');
    }

    public function __construct(string $value, string ...$values)
    {
        $this->values = [$value, ...$values];
    }

    /**
     * @return array<int, string>
     */
    public function values(): array
    {
        return $this->values;
    }
}
