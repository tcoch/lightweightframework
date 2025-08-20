<?php

namespace App\Tests\Request;

use LightWeightFramework\Http\Request\Request;
use LightWeightFramework\LightWeightFramework;
use PHPUnit\Framework\TestCase;

class RequestMethodTest extends TestCase
{
    public function testGetRequestWithSimulatedRequest(): void
    {
        // Through simulated request
        $request = Request::createFromGlobals()
            ->setRequestUri("/HttpMethod.php")
            ->setRequestMethod("GET");
        $response = (new LightWeightFramework())->handle($request);

        self::assertSame(200, $response->getReturnCode());
        self::assertSame('$_SERVER GETRequest GET', $response->getContent());
    }

    public function testGetRequestWithCurl(): void
    {
        // Through cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://localhost/HttpMethod.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);

        self::assertSame(200, curl_getinfo($ch, CURLINFO_HTTP_CODE));
        self::assertSame('$_SERVER GETRequest GET', $output);

        curl_close($ch);
    }

    public function testPostRequestWithSimulatedRequest(): void
    {
        // Through simulated request
        $request = Request::createFromGlobals()
            ->setRequestUri("/HttpMethod.php")
            ->setRequestMethod("POST");
        $response = (new LightWeightFramework())->handle($request);

        self::assertSame(200, $response->getReturnCode());
        self::assertSame('$_SERVER POSTRequest POST', $response->getContent());
    }

    public function testPostRequestWithCurl(): void
    {
        // Through cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://localhost/HttpMethod.php");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);

        self::assertSame(200, curl_getinfo($ch, CURLINFO_HTTP_CODE));
        self::assertSame('$_SERVER POSTRequest POST', $output);

        curl_close($ch);
    }
}
