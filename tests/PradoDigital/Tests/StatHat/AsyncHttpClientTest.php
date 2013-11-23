<?php

namespace PradoDigital\Tests\StatHat;

use PradoDigital\StatHat\AsyncHttpClient;

require_once __DIR__.'/../../../mocks.php';

class AsyncHttpClientTest extends \PHPUnit_Framework_TestCase
{
    private $client;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->client = new AsyncHttpClient();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->client = null;
    }

    public function testPost()
    {
        $expectedRequest = $this->mockRequestContents('{"key":"value"}');
        $this->client->post('/', array('key' => 'value'));
        $actualRequest = file_get_contents(__DIR__.'/../../../socket_mock_contents.txt');
        $this->assertEquals($expectedRequest, $actualRequest);
    }

    private function mockRequestContents($body)
    {
        $request  = "POST / HTTP/1.1\r\n";
        $request .= "Host: api.stathat.com\r\n";
        $request .= "User-Agent: PHP StatHat Bindings/1.x (+https://github.com/pradodigital/php-stathat-bindings)\r\n";
        $request .= "Content-Type: application/json\r\n";
        $request .= "Content-Length: ".strlen($body)."\r\n";
        $request .= "Connection: Close\r\n";
        $request .= "\r\n";
        $request .= $body;

        return $request;
    }
}
