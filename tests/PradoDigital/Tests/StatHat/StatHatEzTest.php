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
}
