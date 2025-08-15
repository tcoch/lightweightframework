<?php

namespace LightWeightFramework\Http\Response\Header;

use LightWeightFramework\Http\Response\ResponseHeaders;

class LocationHeader implements ResponseHeaderInterface
{
    private ?string $location = null;

    public function determineValue(): void
    {
        foreach (headers_list() as $header) {
            if (\str_contains(strtoupper($header), strtoupper('Location'))) {
                $this->location = $header;
            }
        }
    }

    public function send(): void
    {
        if (!\is_null($this->location)) {
            header(ResponseHeaders::HEADER_LOCATION . ": " . $this->location);
        }
    }
}
