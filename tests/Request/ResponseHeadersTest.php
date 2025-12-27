<?php

namespace App\Tests\Request;

use LightWeightFramework\Http\Request\Request;
use LightWeightFramework\Http\Response\Response;
use LightWeightFramework\LightWeightFramework;
use LightWeightFramework\Routing\RouteCollection;
use PHPUnit\Framework\TestCase;

/**
 * cURL request is mandatory / cannot simulate request and response here
 * Simulating request does no send headers
 */
class ResponseHeadersTest extends TestCase
{
    public function testNormalContentType(): void
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://localhost/Procedural.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_exec($ch);
        $headers = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

        self::assertSame("text/html; charset=utf-8", $headers);
    }

    public function testContentTypeForced(): void
    {
        $phpFileContent = <<<EOD
<?php

header("Content-Type: application/json");

echo __DIR__;
EOD;

        $fileName = "testContentTypeForced.php";
        $filePath = __DIR__ . "/../../src/Controller/$fileName";
        file_put_contents($filePath, $phpFileContent);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://localhost/testContentTypeForced.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_exec($ch);
        $headers = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

        self::assertSame("application/json", $headers);

        unlink($filePath);
    }

    public function testContentTypeForcedDifferentCase(): void
    {
        $phpFileContent = <<<EOD
<?php

header("Content-type: application/json");

echo __DIR__;
EOD;

        $fileName = "testContentTypeForced.php";
        $filePath = __DIR__ . "/../../src/Controller/$fileName";
        file_put_contents($filePath, $phpFileContent);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://localhost/testContentTypeForced.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_exec($ch);
        $headers = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

        self::assertSame("application/json", $headers);

        unlink($filePath);
    }

    public function testStatusCodeValidity(): void
    {
        $response = new Response("", 599);
        $response->getHeaders()->sendHeaders();
        self::assertSame(599, $response->getHeaders()->statusCode);
        $response = new Response("", 100);
        $response->getHeaders()->sendHeaders();
        self::assertSame(100, $response->getHeaders()->statusCode);

        $response = new Response("", 99);
        $this->expectException(\LogicException::class);
        $response->getHeaders()->sendHeaders();
        $response = new Response("", 600);
        $this->expectException(\LogicException::class);
        $response->getHeaders()->sendHeaders();
    }
}
