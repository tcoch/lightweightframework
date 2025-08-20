<?php

namespace App\Controller;

use LightWeightFramework\Http\Response\Response;

return (new NotProcedural())->renderResponse();

class NotProcedural
{
    public function renderResponse(): Response
    {
        return new Response("HTML content");
    }

    public function route(): Response
    {
        return new Response( __CLASS__ . " : Route test.");
    }

    public function otherRoute(): Response
    {
        return new Response( __CLASS__ . " : Other route test.");
    }
}
