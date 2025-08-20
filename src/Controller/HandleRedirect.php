<?php

namespace App\Controller;

use LightWeightFramework\Http\Response\RedirectResponse;
use LightWeightFramework\Http\Response\Response;

return (new HandleRedirect())->renderText();

class HandleRedirect
{
    public function renderText(): string
    {
        return "HTML raw content goes here";
    }

    public function redirect(): Response
    {
        return new RedirectResponse("/Procedural");
    }

    public function unprocessableRedirect(): Response
    {
        return new RedirectResponse("/NonExistingPath");
    }
}
