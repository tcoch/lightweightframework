<?php

namespace LightWeightFramework\Http\Response;

class RedirectResponse extends Response
{
    public function __construct(string $location = '/')
    {
        parent::__construct("", 302);

        $this->content = <<<EOD
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="refresh" content="0;url=\'$location\'" />

        <title>Redirecting to $location</title>
    </head>
    <body>
        Redirecting to <a href="$location">$location</a>.
    </body>
</html>
EOD;

        $this->headers->location = $location;
    }
}
