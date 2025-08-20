<?php

namespace App\Tests\Request;

use LightWeightFramework\Http\Request\Request;
use LightWeightFramework\LightWeightFramework;
use PHPUnit\Framework\TestCase;

class RequestMethodTest extends TestCase
{
    public function testGetRequestWithSimulatedRequest(): void
    {
        $request = Request::createFromGlobals()
            ->setRequestUri("/HttpMethod.php")
            ->setRequestMethod("GET");
        $response = (new LightWeightFramework())->handle($request);

        self::assertSame(200, $response->getReturnCode());
        self::assertSame('$_SERVER GETRequest GET', $response->getContent());
    }

    public function testGetRequestWithCurl(): void
    {
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
        $request = Request::createFromGlobals()
            ->setRequestUri("/HttpMethod.php")
            ->setRequestMethod("POST");
        $response = (new LightWeightFramework())->handle($request);

        self::assertSame(200, $response->getReturnCode());
        self::assertSame('$_SERVER POSTRequest POST', $response->getContent());
    }

    public function testPostRequestWithCurl(): void
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://localhost/HttpMethod.php");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);

        self::assertSame(200, curl_getinfo($ch, CURLINFO_HTTP_CODE));
        self::assertSame('$_SERVER POSTRequest POST', $output);

        curl_close($ch);
    }

    public function testPostRequestWithDataWithSimulatedRequest(): void
    {
        $request = Request::createFromGlobals()
            ->setRequestUri("/HttpMethod.php")
            ->setRequestMethod("POST")
            ->addPostData("foo", "bar");
        $response = (new LightWeightFramework())->handle($request);

        self::assertStringContainsString("[foo] => bar", $response->getContent());
        self::assertStringContainsString('Request value: bar', $response->getContent());
    }

    public function testPostRequestWithDataWithCurl(): void
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://localhost/HttpMethod.php");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ["foo" => "bar"]);
        $output = curl_exec($ch);

        self::assertStringContainsString('[foo] => bar', $output);
        self::assertStringContainsString('Request value: bar', $output);

        curl_close($ch);
    }
}
