<?php

namespace LightWeightFramework\Superglobals;

/**
 * Representation of the $_SERVER global variable
 */
class Server
{
    private string $requestUri;

    private string $requestMethod;

    public function __construct()
    {
        $this->requestUri = \is_string($_SERVER['REQUEST_URI'] ?? "") ? $_SERVER['REQUEST_URI'] ?? "" : '';
        $this->requestMethod = \is_string($_SERVER['REQUEST_METHOD'] ?? "") ? $_SERVER['REQUEST_METHOD'] ?? "" : '';
    }

    public function setRequestUri(string $requestUri): void
    {
        $this->requestUri = $requestUri;
        $_SERVER['REQUEST_URI'] = $requestUri;
    }

    public function getRequestUri(): string
    {
        return $this->requestUri;
    }

    public function setRequestMethod(string $requestMethod): void
    {
        $this->requestMethod = $requestMethod;
        $_SERVER['REQUEST_METHOD'] = $requestMethod;
    }

    public function getRequestMethod(): string
    {
        return $this->requestMethod;
    }
}
