<?php

namespace App\Service;

return (new ServiceA())->do();

class ServiceA
{
    public string $name = "Service A";

    public function do(): string
    {
        return "Do";
    }

    public static function staticDo(): string
    {
        return "Static Do";
    }
}
