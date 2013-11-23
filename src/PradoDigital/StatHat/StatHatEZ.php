<?php

/*
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
     * @var array
     */
    private $buffer;

    /**
     * @var string
     */
    private $ezKey;

    /**
     * @var HttpClientInterface
     */
    private $client;

    /**
     * Constructor.
     *
     * @param string $ezKey The EZ Key to use for posting
     */
    public function __construct($ezKey, HttpClientInterface $client)
    {
        $this->buffer = array();
        $this->setEzKey($ezKey);
        $this->setClient($client);

        register_shutdown_function(array($this, 'postBatch'));
    }

    /**
     * {@inheritdoc}
     */
    public function count($stat, $count, $timestamp = null)
    {
        $this->buffer[] = array('stat' => $stat, 'count' => $count, 't' => $timestamp ?: time());

        return $this;
    }

    /**
     * {@inheritdoc}
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
        $params = array('ezkey' => $this->getEzKey(), 'data' => $this->getBuffer());
        return $this->getClient()->post('/ez', $params);
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
     * Gets the stored buffer.
     *
     * @return array
     */
    public function getBuffer()
    {
        return $this->buffer;
    }
}
