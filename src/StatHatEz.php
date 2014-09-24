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
class StatHatEz implements StatHatEzInterface
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
        $this->client = $client;
        $this->setEzKey($ezKey);

        register_shutdown_function([$this, 'postBatch']);

        $this->resetBuffer();
    }

    /**
     * @see \PradoDigital\StatHat\StatHatEzInterface::setEzKey()
     */
    public function setEzKey($ezKey)
    {
        $this->ezKey = $ezKey;
    }

    /**
     * @see \PradoDigital\StatHat\StatHatEzInterface::ezCount()
     */
    public function ezCount($statName, $count = 1, $timestamp = null)
    {
        $this->buffer[$this->ezKey][] = [
            'stat' => $statName,
            'count' => $count,
            't' => $timestamp ?: time()
        ];

        return $this;
    }

    /**
     * @see \PradoDigital\StatHat\StatHatEzInterface::ezValue()
     */
    public function ezValue($statName, $value, $timestamp = null)
    {
        $this->buffer[$this->ezKey][] = [
            'stat' => $statName,
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
        foreach ($this->buffer as $ezKey => $stats) {

            if (!empty($stats)) {

                $params = [
                    'ezkey' => $ezKey,
                    'data' => $stats
                ];

                $isPosted = $this->client->post('/ez', $params, 'application/json');

                if ($isPosted) {
                    $this->resetBuffer($ezKey);
                }
            }
        }
    }

    /**
     * Empties the buffer.
     *
     * @return StatHatEz
     */
    private function resetBuffer($ezKey = null)
    {
        if ($ezKey === null) {

            $this->buffer = [];

        } else {

            $this->buffer[$ezKey] = [];
        }

        return $this;
    }
}
