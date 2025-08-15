<?php

namespace LightWeightFramework\Http\Response;

use LightWeightFramework\Container\Container;
use LightWeightFramework\Http\Response\Header\ContentTypeHeader;
use LightWeightFramework\Http\Response\Header\LocationHeader;
use LightWeightFramework\Http\Response\Header\ResponseHeaderInterface;

class ResponseHeaders
{
    public const HEADER_LOCATION = 'Location';
    public const HEADER_CONTENT_TYPE = 'Content-Type';

    public int $statusCode;

    public ?string $location = null;

    /** @var ResponseHeaderInterface[] $headers */
    public array $headers = [];

    public function __construct()
    {
        $this->registerHeaders();
    }

    /**
     * Registers every class that implements ResponseHeaderInterface to handle the related header
     * @return void
     */
    protected function registerHeaders(): void
    {
        foreach (get_declared_classes() as $class) {
            if (is_a($class, ResponseHeaderInterface::class, true)) {
                $this->headers[] = new $class;
            }
        }
    }

    public function sendHeaders(): void
    {
        foreach ($this->headers as $header) {
            $header->determineValue();
        }

        switch ($this->statusCode) {
            case 200:
                header("HTTP/1.0 200 OK");
                break;
            case 302:
                header("HTTP/1.0 302 Found");
                break;
            case 404:
                header("HTTP/1.0 404 Not Found");
                break;
        }

        foreach ($this->headers as $header) {
            $header->send();
        }
    }
}
