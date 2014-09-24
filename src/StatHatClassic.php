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
class StatHatClassic implements StatHatClassicInterface
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
        $this->client = $client;
        $this->setUserKey($userKey);

        register_shutdown_function([$this, 'postBatch']);

        $this->resetBuffer();
    }

    /** (non-PHPdoc)
     * @see \PradoDigital\StatHat\StatHatClassicInterface::setUserKey()
     */
    public function setUserKey($userKey)
    {
        $this->userKey = $userKey;
    }

    /**
     * @see \PradoDigital\StatHat\StatHatClassicInterface::count()
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
     * @see \PradoDigital\StatHat\StatHatClassicInterface::value()
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

            $this->resetBuffer();
        }
    }

    /**
     * Empties the buffer.
     *
     * @return StatHatClassic
     */
    private function resetBuffer()
    {
        $this->buffer = ['counters' => [], 'values' => []];

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
