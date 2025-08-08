<?php

namespace App\Controller;

use LightWeightFramework\Http\Response\RedirectResponse;
use LightWeightFramework\Http\Response\Response;

class HandleRedirect
{
    public function redirect(): Response
    {
        return new RedirectResponse("/Procedural");
    }

    public function unprocessableRedirect(): Response
    {
        return new RedirectResponse("/NonExistingPath");
    }
}
