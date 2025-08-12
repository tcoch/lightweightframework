<?php

use App\Service\ServiceA;
use LightWeightFramework\LightWeightFramework;

$container = LightWeightFramework::getContainer();

$serviceA = $container->get(ServiceA::class);

echo $serviceA->do();

echo $serviceA->staticDo();
echo $serviceA::staticDo();
