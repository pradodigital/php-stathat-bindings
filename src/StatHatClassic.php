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
 * StatHatClassic updates stats via the Classic API. It buffers messages internally and
 * sends them all by registering a shutdown function.
 *
 * @author Jose Prado <cowlby@me.com>
 */
class StatHatClassic implements StatHatInterface
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
     * The internal user key used for POSTing
     *
     * @var string
     */
    private $userKey;

    /**
     * Constructor.
     *
     * @param HttpClientInterface $client An HttpClientInterface instance
     * @param string $userKey The User Key to use for posting
     */
    public function __construct(HttpClientInterface $client, $userKey)
    {
        $this->buffer = ['counters' => [], 'values' => []];
        $this->client = $client;
        $this->userKey = $userKey;

        register_shutdown_function([$this, 'postBatch']);
    }

    /**
     * Queues an update to a counter stat.
     *
     * @param string $stat   The unique stat key
     * @param int $count     The number to count
     * @param int $timestamp Optional timestamp, defaults to time()
     *
     * @return StatHatInterface
     */
    public function count($stat, $count = 1, $timestamp = null)
    {
        $this->buffer['counters'][] = [
            'key' => $stat,
            'ukey' => $this->userKey,
            'count' => $count,
            't' => $timestamp ?: time()
        ];

        return $this;
    }

    /**
     * Queues an update to a value tracker.
     *
     * @param string $stat   The unique stat key
     * @param int $value     The value to track
     * @param int $timestamp Optional timestamp, defaults to time()
     *
     * @return StatHatInterface
     */
    public function value($stat, $value, $timestamp = null)
    {
        $this->buffer['values'][] = [
            'key' => $stat,
            'ukey' => $this->userKey,
            'value' => $value,
            't' => $timestamp ?: time()
        ];

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

            foreach ($this->buffer['counters'] as $params) {
                $this->client->post('/c', $params);
            }

            foreach ($this->buffer['values'] as $params) {
                $this->client->post('/v', $params);
            }

            $this->clearBuffer();
        }
    }

    /**
     * Empties the buffer.
     *
     * @return \PradoDigital\StatHat\StatHatClassic
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
        return count($this->buffer['counters']) > 0 || count($this->buffer['values'] > 0);
    }
}
