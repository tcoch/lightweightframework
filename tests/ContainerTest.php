<?php

namespace App\Tests;

use App\Service\ServiceA;
use App\Service\SubService\SubServiceA;
use LightWeightFramework\Exception\ServiceNotFoundException;
use LightWeightFramework\Http\Request\Request;
use LightWeightFramework\LightWeightFramework;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function testRequestNeedingAService(): void
    {
        $request = Request::createFromGlobals()->setRequestUri("/ServiceClass.php");
        $response = (new LightWeightFramework())->handle($request);
        self::assertSame(200, $response->getReturnCode());
    }

    public function testServiceMethodCall(): void
    {
        $container = LightWeightFramework::getContainer();
        /** @var ServiceA $service */
        $service = $container->get(ServiceA::class);
        self::assertSame('Do', $service->do());
        $this->expectException(\Error::class);
        self::assertSame('Do', $service::do()); // @phpstan-ignore method.staticCall
    }

    public function testServiceStaticMethodCall(): void
    {
        $container = LightWeightFramework::getContainer();
        /** @var ServiceA $service */
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

    public function testSubServiceExistsInContainer(): void
    {
        $container = LightWeightFramework::getContainer();
        self::assertInstanceOf(ServiceA::class, $container->get('App\Service\ServiceA'));
        self::assertInstanceOf(SubServiceA::class, $container->get('App\Service\SubService\SubServiceA'));
        self::assertInstanceOf(ServiceA::class, $container->get(ServiceA::class));
        self::assertInstanceOf(SubServiceA::class, $container->get(SubServiceA::class));
    }
}
