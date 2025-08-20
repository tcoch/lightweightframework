<?php

namespace LightWeightFramework\Superglobals;

/**
 * Representation of the $_POST global variable
 */
class Post
{
    private array $parameters = [];

    public function __construct()
    {
        $this->parameters = $_POST;
    }

    public function set(string $key, mixed $value): self
    {
        $this->parameters[$key] = $value;
        $_POST[$key] = $value;

        return $this;
    }

    public function getValue(string $key)
    {
        return $this->parameters[$key] ?? null;
    }

    public function getValues(): array
    {
        return $this->parameters;
    }
}
