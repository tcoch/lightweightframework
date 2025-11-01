<?php

namespace App\Tests\Request;

use LightWeightFramework\Http\Request\Request;
use LightWeightFramework\Http\Response\Response;
use LightWeightFramework\LightWeightFramework;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function testSuccessfulResponse(): void
    {
        $response = new Response("Dummy response", 200);
        self::assertSame(200, $response->getReturnCode());

        $this->expectOutputString('Dummy response');
        $response->send();
    }

    public function testFailedResponse(): void
    {
        $response = new Response("Dummy failed response", 404);
        self::assertSame(404, $response->getReturnCode());

        $this->expectOutputString('Dummy failed response');
        $response->send();
    }

    public function testRedirectResponse(): void
    {
        $f = new LightWeightFramework();

        $request = Request::createFromGlobals()->setRequestUri("/ClassRedirect");
        $response = $f->handle($request);

        $this->expectOutputRegex('/.*/');
        $response->send();

        self::assertSame(302, $response->getReturnCode());
    }

    public function testResponseUri(): void
    {
        $f = new LightWeightFramework();

        $request = Request::createFromGlobals()->setRequestUri("/DirectAccess.php");
        $response = $f->handle($request);

        self::assertSame('/DirectAccess.php', $response->getUri());
    }

    public function testResponseUriInCaseOfRedirect(): void
    {
        $f = new LightWeightFramework();

        $request = Request::createFromGlobals()->setRequestUri("/ClassRedirect");
        $response = $f->handle($request);

        self::assertSame('/ClassRedirect', $response->getUri());
    }
}
