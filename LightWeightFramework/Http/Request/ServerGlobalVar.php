<?php

namespace LightWeightFramework\Http\Request;

class ServerGlobalVar
{
    private string $requestUri;

    private string $queryString;

    public function __construct()
    {
        $this->requestUri = \is_string($_SERVER['REQUEST_URI'] ?? "") ? $_SERVER['REQUEST_URI'] ?? "" : '';
        $this->queryString = \is_string($_SERVER['QUERY_STRING'] ?? "") ? $_SERVER['QUERY_STRING'] ?? "" : '';
    }

    public function setRequestUri(string $requestUri): void
    {
        $this->requestUri = $requestUri;
    }

    public function getRequestUri(): string
    {
        return $this->requestUri;
    }

    public function getQueryString(): string
    {
        return $this->queryString;
    }
}
