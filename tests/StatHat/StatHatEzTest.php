<?php

namespace PradoDigital\Tests\StatHat;

use PradoDigital\StatHat\StatHatEz;

class StatHatEzTest extends \PHPUnit_Framework_TestCase
{
    const MOCK_EZKEY = 'MockStatHatEZKey';

    private $mockClient;
    private $statHat;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->mockClient = $this->getMock('PradoDigital\StatHat\HttpClientInterface', array('post'));
        $this->statHat = new StatHatEz($this->mockClient, self::MOCK_EZKEY);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->statHat = null;
    }

    public function testPostBatchWhenEmpty()
    {
        $this->mockClient
            ->expects($this->never())
            ->method('post')
        ;

        $this->statHat->postBatch();
    }

    public function testPostBatchWithStats()
    {
        $this->statHat->ezCount('mock stat count', 1, 1362772440);
        $this->statHat->ezValue('mock stat avg', 1, 1362772440);
        $params = array(
            'ezkey' => self::MOCK_EZKEY,
            'data' => array(
                array('stat' => 'mock stat count', 'count' => 1, 't' => 1362772440),
                array('stat' => 'mock stat avg', 'value' => 1, 't' => 1362772440)
            )
        );

        $this->mockClient
            ->expects($this->once())
            ->method('post')
            ->with('/ez', $this->equalTo($params), 'application/json')
            ->will($this->returnValue(true))
        ;

        $this->statHat->postBatch();
    }
}
