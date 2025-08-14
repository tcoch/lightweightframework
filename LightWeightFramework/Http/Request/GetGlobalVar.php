<?php

namespace LightWeightFramework\Http\Request;

/**
 * Representation of the $_GET global variable
 */
class GetGlobalVar
{
    /** @var string[] $parameters */
    private array $parameters = [];

    public function __construct()
    {
        $queryString = \is_string($_SERVER['QUERY_STRING'] ?? "") ? $_SERVER['QUERY_STRING'] ?? "" : '';
        $this->setQueryString($queryString);
    }

    public function setQueryString(string $queryString): self
    {
        if (\str_contains($queryString, '?')) {
            $queryString = \explode('?', $queryString)[1];
        }

        if (!\str_contains($queryString, '=')) {
            return $this;
        }

        $allParameters = \explode('&', $queryString);
        foreach ($allParameters as $parameter) {
            $explodedParameter = explode('=', $parameter);
            $this->parameters[$explodedParameter[0]] = $explodedParameter[1];

            // Override $_GET global variable for tests purposes (PHPUnit erases it)
            $_GET[$explodedParameter[0]] = $explodedParameter[1];
        }

        return $this;
    }
}
