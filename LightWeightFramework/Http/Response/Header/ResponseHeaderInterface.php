<?php

namespace LightWeightFramework\Http\Response\Header;

interface ResponseHeaderInterface
{
    /**
     * Return the correct value for the header
     * @return void
     */
    public function determineValue(): void;

    /**
     * Performs the `header()` method
     * @return void
     */
    public function send(): void;
}
