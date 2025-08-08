<?php

namespace App\Controller;

use LightWeightFramework\Http\Response\Response;

class NotProcedural
{
    public function route(): Response
    {
        return new Response( __CLASS__ . " : Route test.");
    }

    public function otherRoute(): Response
    {
        return new Response( __CLASS__ . " : Other route test.");
    }
}
