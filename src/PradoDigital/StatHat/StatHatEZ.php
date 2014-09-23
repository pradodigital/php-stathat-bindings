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

use GuzzleHttp\ClientInterface;

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
     * The internal ClientInterface used to POST
     *
     * @var ClientInterface
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
    public function __construct(ClientInterface $client, $ezKey)
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
    public function count($stat, $count, $timestamp = null)
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

            $response = $this->client->post('https://api.stathat.com/ez', [
                'json' => [
                    'ezkey' => $this->ezKey,
                    'data' => $this->buffer
                ]
            ]);

            print_r($response->getEffectiveUrl());
            print_r($response->getStatusCode());
            print_r($response->getReasonPhrase());
            print_r($response->getHeaders());

            if ($response->getStatusCode() === '200') {
                echo 'successful';
                $this->clearBuffer();
            }
        }
    }

    /**
     * Empties the buffer.
     *
     * @return \PradoDigital\StatHat\StatHatEZ
     */
    public function clearBuffer()
    {
        $this->buffer = [];

        return $this;
    }

    /**
     * Checks whether or not there are stats to send.
     *
     * @return boolean
     */
    public function hasStats()
    {
        return count($this->buffer) > 0;
    }
}
