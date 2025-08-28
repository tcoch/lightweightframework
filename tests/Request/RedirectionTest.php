<?php

namespace App\Tests\Request;

use LightWeightFramework\Exception\OutputBufferException;
use LightWeightFramework\Http\Request\Request;
use LightWeightFramework\LightWeightFramework;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

class RedirectionTest extends TestCase
{
    public function testRedirectResponse(): void
    {
        $f = new LightWeightFramework();

        $request = Request::createFromGlobals()->setRequestUri("/ClassRedirect");
        $response = $f->handle($request);

        self::assertSame(302, $response->getReturnCode());
        $location = $response->getHeaders()->location;
        self::assertStringContainsString("<meta http-equiv=\"refresh\" content=\"0;url=$location\" />",
            $response->getContent());

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
        self::assertStringContainsString("<meta http-equiv=\"refresh\" content=\"0;url=$location\" />",
            $response->getContent());

        $request->setRequestUri($location);
        $response = $f->handle($request);

        self::assertSame(404, $response->getReturnCode());
    }

    /**
     * Simulated request cannot be tested because of the exit() after the header()
     * @return void
     */
    public function testRedirectWithProceduralHeader(): void
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://localhost/ProceduralRedirect.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);

        self::assertSame(302, curl_getinfo($ch, CURLINFO_HTTP_CODE));
        self::assertSame('Some text', $output);

        curl_close($ch);
    }

    /**
     * Simulated request cannot be tested because of the exit() after the header()
     * @return void
     */
    public function testFollowRedirectWithProceduralHeader(): void
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://localhost/ProceduralRedirect.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $output = curl_exec($ch);

        self::assertSame(200, curl_getinfo($ch, CURLINFO_HTTP_CODE));
        self::assertSame('In direct access. Running procedural script(s).', $output);

        curl_close($ch);
    }
}
