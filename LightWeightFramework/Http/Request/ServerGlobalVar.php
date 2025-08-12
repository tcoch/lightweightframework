<?php

namespace LightWeightFramework\Http\Request;

/**
 * Representation of the $_SERVER global variable
 */
class ServerGlobalVar
{
    private string $requestUri;

    public function __construct()
    {
        $this->requestUri = \is_string($_SERVER['REQUEST_URI'] ?? "") ? $_SERVER['REQUEST_URI'] ?? "" : '';
    }

    public function setRequestUri(string $requestUri): void
    {
        $this->requestUri = $requestUri;
    }

    public function getRequestUri(): string
    {
        return $this->requestUri;
    }
}
