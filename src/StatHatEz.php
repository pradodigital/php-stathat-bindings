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
 * StatHatEz updates stats via the EZ API. It buffers messages internally and
 * sends them all by registering a shutdown function.
 *
 * @author Jose Prado <cowlby@me.com>
 */
class StatHatEz implements StatHatInterface
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
     * @param HttpClientInterface $client An HttpClientInterface instance
     * @param string $ezKey The EZ Key to use for posting
     */
    public function __construct(HttpClientInterface $client, $ezKey)
    {
        $this->buffer = [];
        $this->client = $client;
        $this->ezKey = $ezKey;

        register_shutdown_function([$this, 'postBatch']);
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
    public function count($stat, $count = 1, $timestamp = null)
    {
        $this->buffer[] = ['stat' => $stat, 'count' => $count, 't' => $timestamp ?: time()];

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
        $this->buffer[] = ['stat' => $stat, 'value' => $value, 't' => $timestamp ?: time()];

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

            $params = [
                'ezkey' => $this->ezKey,
                'data' => $this->buffer
            ];

            $isPosted = $this->client->post('/ez', $params, 'application/json');

            if ($isPosted) {
                $this->clearBuffer();
            }
        }
    }

    /**
     * Empties the buffer.
     *
     * @return \PradoDigital\StatHat\StatHatEZ
     */
    private function clearBuffer()
    {
        $this->buffer = [];

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
