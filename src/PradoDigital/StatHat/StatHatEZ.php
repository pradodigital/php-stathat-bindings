<?php

/**
 * This file is part of the PHP StatHat Bindings package.
 *
 * (c) Jose Prado <cowlby@me.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PradoDigital\StatHat;

/**
 * StatHatEZ updates stats via the EZ API. It buffers messages internally and
 * sends them all by registering a shutdown function.
 *
 * @author Jose Prado <cowlby@me.com>
 */
class StatHatEZ implements StatHatInterface
{
    /**
     * The internal buffer of stats.
     *
     * @var array
     */
    private $buffer;

    /**
     * The internal HttpClientInterface used to POST
     *
     * @var HttpClientInterface
     */
    private $client;

    /**
     * The internal EZ key used for POSTing
     *
     * @var string
     */
    private $ezKey;

    /**
     * Constructor.
     *
     * @param string $ezKey The EZ Key to use for posting
     */
    public function __construct(HttpClientInterface $client, $ezKey)
    {
        $this->buffer = array();
        $this->setClient($client);
        $this->setEzKey($ezKey);

        register_shutdown_function(array($this, 'postBatch'));
    }

    /**
     * Queues an update to a counter stat.
     *
     * @param string $stat   The unique stat name
     * @param int $count     The number to count
     * @param int $timestamp Optional timestamp, defaults to time()
     *
     * @return StatHatInterface
     */
    public function count($stat, $count, $timestamp = null)
    {
        $this->buffer[] = array('stat' => $stat, 'count' => $count, 't' => $timestamp ?: time());

        return $this;
    }

    /**
     * Queues an update to a value tracker.
     *
     * @param string $stat   The unique stat name
     * @param int $value     The value to track
     * @param int $timestamp Optional timestamp, defaults to time()
     *
     * @return StatHatInterface
     */
    public function value($stat, $value, $timestamp = null)
    {
        $this->buffer[] = array('stat' => $stat, 'value' => $value, 't' => $timestamp ?: time());

        return $this;
    }

    /**
     * Flushes the buffer by POSTing the stats in JSON format to Stat Hat.
     *
     * @return boolean
     */
    public function postBatch()
    {
        if ($this->hasStats()) {

            $params = array(
                'ezkey' => $this->getEzKey(),
                'data' => $this->getBuffer()
            );

            $isPosted = $this->getClient()->post('/ez', $params);

            if ($isPosted) {
                $this->clearBuffer();
            }
        }
    }

    /**
     * Set the HTTP client.
     *
     * @param string $client The HTTP client to use
     *
     * @return StatHatInterface
     */
    public function setClient(HttpClientInterface $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Gets the HTTP client.
     *
     * @return HttpClientInterface The stored HTTP Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set the EZ Key.
     *
     * @param string $ezKey The EZ Key to use for posting
     *
     * @return StatHatInterface
     */
    public function setEzKey($ezKey)
    {
        $this->ezKey = $ezKey;

        return $this;
    }

    /**
     * Gets the EZ Key.
     *
     * @return string The stored EZ Key
     */
    public function getEzKey()
    {
        return $this->ezKey;
    }

    /**
     * Gets the stored buffer.
     *
     * @return array
     */
    public function getBuffer()
    {
        return $this->buffer;
    }

    /**
     * Empties the buffer.
     *
     * @return \PradoDigital\StatHat\StatHatEZ
     */
    private function clearBuffer()
    {
        $this->buffer = array();

        return $this;
    }

    /**
     * Checks whether or not there are stats to send.
     *
     * @return boolean
     */
    private function hasStats()
    {
        return count($this->buffer) > 0;
    }
}
