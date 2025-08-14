<?php

namespace App\Tests;

use App\Service\ServiceA;
use LightWeightFramework\Exception\ServiceNotFoundException;
use LightWeightFramework\Http\Request\Request;
use LightWeightFramework\LightWeightFramework;
use PHPUnit\Framework\TestCase;

/**
 * @phpstan-ignore method.nonObject
 * @phpstan-ignore staticMethod.nonObject
 */
class ContainerTest extends TestCase
{
    public function testRequestNeedingAService(): void
    {
        $request = Request::createFromGlobals()->setRequestUri("/ServiceClass.php");
        $response = new LightWeightFramework()->handle($request);
        self::assertSame(200, $response->getReturnCode());
    }

    public function testServiceMethodCall(): void
    {
        $container = LightWeightFramework::getContainer();
        $service = $container->get(ServiceA::class);
        self::assertSame('Do', $service->do());
        $this->expectException(\Error::class);
        self::assertSame('Do', $service::do());
    }

    public function testServiceStaticMethodCall(): void
    {
        $container = LightWeightFramework::getContainer();
        $service = $container->get(ServiceA::class);
        self::assertSame('Static Do', $service::staticDo());
        self::assertSame('Static Do', $service::staticDo());
    }

    public function testGetNonExistingServiceFromContainer(): void
    {
        $container = LightWeightFramework::getContainer();
        $this->expectException(ServiceNotFoundException::class);
        $container->get('App\Service\NonExistingService');
    }
}
