<?php

namespace PradoDigital\Tests\StatHat;

use PradoDigital\StatHat\StatHatEZ;

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
        $this->statHat = new StatHatEZ($this->mockClient, self::MOCK_EZKEY);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->statHat = null;
    }

    public function testEzKeyGettersAndSetters()
    {
        $this->assertSame(self::MOCK_EZKEY, $this->statHat->getEzKey());

        $newEzKey = 'NewMockStatHatEZKey';
        $this->statHat->setEzKey($newEzKey);
        $this->assertSame($newEzKey, $this->statHat->getEzKey());
    }

    public function testClientGettersAndSetters()
    {
        $this->assertSame($this->mockClient, $this->statHat->getClient());

        $newClient = $this->getMock('PradoDigital\StatHat\HttpClientInterface', array('post'));
        $this->statHat->setClient($newClient);
        $this->assertSame($newClient, $this->statHat->getClient());
    }

    public function testBufferGetter()
    {
        $this->assertEmpty($this->statHat->getBuffer());

        $this->statHat->count('mock stat', 1);
        $this->assertCount(1, $this->statHat->getBuffer());

        $this->statHat->value('mock stat', 100);
        $this->assertCount(2, $this->statHat->getBuffer());
    }

    public function testPostBatchWhenEmpty()
    {
        $params = array('ezkey' => self::MOCK_EZKEY, 'data' => array());

        $this->mockClient
            ->expects($this->once())
            ->method('post')
            ->with('/ez', $this->equalTo($params))
            ->will($this->returnValue(true))
        ;

        $this->statHat->postBatch();
    }

    public function testPostBatchWithStats()
    {
        $this->statHat->count('mock stat count', 1, 1362772440);
        $this->statHat->value('mock stat avg', 1, 1362772440);
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
            ->with('/ez', $this->equalTo($params))
            ->will($this->returnValue(true))
        ;

        $this->statHat->postBatch();
    }
}
