<?php

namespace App\Tests\Request;

use LightWeightFramework\Exception\OutputBufferException;
use LightWeightFramework\Http\Request\Request;
use LightWeightFramework\LightWeightFramework;
use LightWeightFramework\Routing\Route;
use LightWeightFramework\Routing\RouteCollection;
use PHPUnit\Framework\TestCase;

/**
 * What's expected :
 * - File 'src/Controller/NotProcedural.php' is available only through a request to /NotProcedural
 * - File 'src/Controller/Procedural.php'    is available only through a request to /Procedural
 * - File 'public/DirectAccess.php'          is available only through a request to /DirectAccess.php
 */
class RequestTest extends TestCase
{
    public function testNotProcedural(): void
    {
        // Through simulated request
        $request = Request::createFromGlobals()->setRequestUri("/testRouting");
        $response = (new LightWeightFramework())->handle($request);

        self::assertSame(200, $response->getReturnCode());
        self::assertSame("App\Controller\NotProcedural : Route test.", $response->getContent());

        // Through cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://localhost/testRouting");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);

        self::assertSame(200, curl_getinfo($ch, CURLINFO_HTTP_CODE));
        self::assertSame("App\Controller\NotProcedural : Route test.", $output);

        curl_close($ch);
    }

    /**
     * Tests that a procedural script can be matched by a route and rendered
     */
    public function testProcedural(): void
    {
        // Through simulated request
        $request = Request::createFromGlobals()->setRequestUri("/Procedural");
        $response = (new LightWeightFramework())->handle($request);

        self::assertSame(200, $response->getReturnCode());
        self::assertSame("In procedural controller. Running procedural script(s).", $response->getContent());

        // Through cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://localhost/Procedural");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);

        self::assertSame(200, curl_getinfo($ch, CURLINFO_HTTP_CODE));
        self::assertSame("In procedural controller. Running procedural script(s).", $output);

        curl_close($ch);
    }

    public function testDirectAccess(): void
    {
        // Through simulated request
        // > Request is direct to the PHP file in 'public' folder
        // > Not intercepted by index.php and by the framework
        $request = Request::createFromGlobals()->setRequestUri("/DirectAccess.php");
        $response = (new LightWeightFramework())->handle($request);

        self::assertSame(404, $response->getReturnCode());

        // Through cURL
        // > File DirectAccess.php is available under 'public' folder
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://localhost/DirectAccess.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);

        self::assertSame(200, curl_getinfo($ch, CURLINFO_HTTP_CODE));
        self::assertSame("In direct access. Running procedural script(s).", $output);

        curl_close($ch);
    }

    public function testRequestNotProcessable(): void
    {
        $route = new Route("/InexistentProceduralScript", 'InexistentProceduralScript.php');
        RouteCollection::getInstance()->addRoute($route);

        $request = Request::createFromGlobals()->setRequestUri("/InexistentProceduralScript");
        $response = (new LightWeightFramework())->handle($request);

        self::assertSame(404, $response->getReturnCode());
        self::assertSame("Couldn't handle request", $response->getContent());
    }

    /**
     * This test covers the case where a procedural script is supposed to be accessible behind a route
     * If the PHP file doesn't exist, an exception is thrown
     * @return void
     */
    public function testRequestToInexistentProceduralScript(): void
    {
        $route = new Route("/InexistentProceduralScript", 'InexistentProceduralScript.php');
        RouteCollection::getInstance()->addRoute($route);

        $request = Request::createFromGlobals()->setRequestUri("/InexistentProceduralScript");
        $response = (new LightWeightFramework())->handle($request);

        self::assertSame(404, $response->getReturnCode());
        self::assertSame("Couldn't handle request", $response->getContent());
    }

    public function testOverrideDirectAccessWithRoute(): void
    {
        $request = Request::createFromGlobals()->setRequestUri("/DirectAccessRoute");
        $response = (new LightWeightFramework())->handle($request);

        self::assertSame(200, $response->getReturnCode());
        self::assertSame("In direct access. Running procedural script(s).", $response->getContent());
    }

    public function testTwoRoutesInOnController(): void
    {
        $f = new LightWeightFramework();

        $request = Request::createFromGlobals()->setRequestUri("/testRouting");
        $response = $f->handle($request);
        self::assertSame("App\Controller\NotProcedural : Route test.", $response->getContent());

        $request = Request::createFromGlobals()->setRequestUri("/testOtherRouting");
        $response = $f->handle($request);
        self::assertSame("App\Controller\NotProcedural : Other route test.", $response->getContent());
    }

    public function indexNotAccessible(): void
    {
        $f = new LightWeightFramework();

        $request = Request::createFromGlobals()->setRequestUri("/index");
        $response = $f->handle($request);
        self::assertSame(404, $response->getReturnCode());

        $request = Request::createFromGlobals()->setRequestUri("/index.php");
        $response = $f->handle($request);
        self::assertSame(404, $response->getReturnCode());

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://localhost/index");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        self::assertSame(404, $output);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://localhost/index.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        self::assertSame(404, $output);
    }

    public function testRouteNotFound(): void
    {
        $f = new LightWeightFramework();

        $request = Request::createFromGlobals()->setRequestUri("/NoRouteFound");
        $response = $f->handle($request);

        self::assertSame(404, $response->getReturnCode());
        self::assertSame("No route found", $response->getContent());
    }

    public function testRedirectResponse(): void
    {
        $f = new LightWeightFramework();

        $request = Request::createFromGlobals()->setRequestUri("/ClassRedirect");
        $response = $f->handle($request);

        self::assertSame(302, $response->getReturnCode());
        $location = $response->getHeaders()->location;
        self::assertStringContainsString("<meta http-equiv=\"refresh\" content=\"0;url=$location\" />", $response->getContent());

        $request->setRequestUri($location);
        $response = $f->handle($request);

        self::assertSame(200, $response->getReturnCode());
    }

    public function testRedirectToNoRouteFound(): void
    {
        $f = new LightWeightFramework();

        $request = Request::createFromGlobals()->setRequestUri("/ErrorOnRedirect");
        $response = $f->handle($request);

        self::assertSame(302, $response->getReturnCode());
        $location = $response->getHeaders()->location;
        self::assertStringContainsString("<meta http-equiv=\"refresh\" content=\"0;url=$location\" />", $response->getContent());

        $request->setRequestUri($location);
        $response = $f->handle($request);

        self::assertSame(404, $response->getReturnCode());
    }

    public function testProceduralRoutingTowardsClassFileWithNoOutput(): void
    {
        $request = Request::createFromGlobals()->setRequestUri("/EmptyClass.php");

        $this->expectException(OutputBufferException::class);
        $response = (new LightWeightFramework())->handle($request);

        self::assertSame(404, $response->getReturnCode());
        self::assertStringContainsString("Unrecoverable output buffer error", $response->getContent());
    }

    public function testProceduralScriptWithNoOutput(): void
    {
        $request = Request::createFromGlobals()->setRequestUri("/EmptyProcedural.php");

        $this->expectException(OutputBufferException::class);
        $response = (new LightWeightFramework())->handle($request);

        self::assertSame(404, $response->getReturnCode());
        self::assertStringContainsString("Unrecoverable output buffer error", $response->getContent());
    }

    public function testGetParameters(): void
    {
        $f = new LightWeightFramework();

        $request = Request::createFromGlobals()->setRequestUri("/Procedural?foo=bar");
        $response = $f->handle($request);

        self::assertStringContainsString("[foo] => bar", $response->getContent());
    }

    public function testMultipleGetParameters(): void
    {
        $f = new LightWeightFramework();

        $request = Request::createFromGlobals()->setRequestUri("/Procedural?foo=bar&foo2=bar2");
        $response = $f->handle($request);

        self::assertStringContainsString("[foo] => bar", $response->getContent());
        self::assertStringContainsString("[foo2] => bar2", $response->getContent());
    }

    public function testClassRendersItself(): void
    {
        $f = new LightWeightFramework();

        $request = Request::createFromGlobals()->setRequestUri("/HandleRedirect.php");
        $response = $f->handle($request);

        self::assertSame("HTML raw content goes here", $response->getContent());
    }

    public function testClassFileReturnsResponse(): void
    {
        $f = new LightWeightFramework();

        $request = Request::createFromGlobals()->setRequestUri("/NotProcedural.php");
        $response = $f->handle($request);

        self::assertSame("HTML content", $response->getContent());
    }
}
