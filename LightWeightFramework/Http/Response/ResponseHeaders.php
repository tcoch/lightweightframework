<?php

namespace LightWeightFramework\Http\Response;

class ResponseHeaders
{
    public const HEADER_LOCATION = 'Location';
    public const HEADER_CONTENT_TYPE = 'Content-Type';

    public int $statusCode;

    public ?string $location = null;

    /** @var string[] $headers */
    public array $headers = [];

    public function sendHeaders(): void
    {
        // Update headers based on latest informations (updated location for example)
        $this->calculateHeaders();

        switch ($this->statusCode) {
            case 200:
                header("HTTP/1.0 200 OK");
                $this->sendContentTypeHeader();
                break;
            case 302:
                header("HTTP/1.0 302 Found");
                $this->sendLocationHeader();
                break;
            case 404:
                header("HTTP/1.0 404 Not Found");
                break;
        }
    }

    private function calculateHeaders(): void
    {
        $this->calculateLocationHeader();
        $this->calculateContentTypeHeader();
    }

    private function sendLocationHeader(): void
    {
        if (\array_key_exists(self::HEADER_LOCATION, $this->headers)) {
            header($this->headers[self::HEADER_LOCATION]);
        }
    }

    private function sendContentTypeHeader(): void
    {
        if (\array_key_exists(self::HEADER_CONTENT_TYPE, $this->headers)) {
            header($this->headers[self::HEADER_CONTENT_TYPE]);
        }
    }

    private function calculateLocationHeader(): void
    {
        foreach (headers_list() as $header) {
            if (str_contains($header, 'Location:')) {
                $this->headers[self::HEADER_LOCATION] = $header;
            }
        }

        if (!\array_key_exists(self::HEADER_LOCATION, $this->headers) && $this->location) {
            $this->headers[self::HEADER_LOCATION] = 'Location: ' . $this->location;
        }
    }

    private function calculateContentTypeHeader(): void
    {
        foreach (headers_list() as $header) {
            if (\str_contains(strtoupper($header), strtoupper('Content-Type'))) {
                $this->headers[self::HEADER_CONTENT_TYPE] = $header;
            }
        }

        if (!\array_key_exists(self::HEADER_CONTENT_TYPE, $this->headers)) {
            $this->headers[self::HEADER_CONTENT_TYPE] = "Content-Type: text/html; charset=utf-8";
        }
    }
}
