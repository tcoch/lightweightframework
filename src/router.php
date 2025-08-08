<?php

return [
    "/testRouting" => [new \App\Controller\NotProcedural(), 'route'],
    "/testOtherRouting" => [new \App\Controller\NotProcedural(), 'otherRoute'],
    "/Procedural" => 'Procedural.php',
    "/DirectAccessRoute" => '../../public/DirectAccess.php',
    '/ClassRedirect' => [new \App\Controller\HandleRedirect(), 'redirect'],
    '/ErrorOnRedirect' => [new \App\Controller\HandleRedirect(), 'unprocessableRedirect'],
    "/unprocessableRoute" => "Unprocessable.php",
];
