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

class StatHat implements StatHatApiInterface, StatHatJsonApiInterface
{
    private $buffer;
    private $client;
    private $accessToken;
    private $ezKey;

    public function __construct(ClientInterface $client, $accessToken, $ezKey)
    {
        $this->buffer = [];
        $this->client = $client;
        $this->accessToken = $accessToken;
        $this->ezKey = $ezKey;

        register_shutdown_function([$this, 'postBatch']);
    }

    public function count($stat, $count = 1, $timestamp = null)
    {
        $this->buffer[] = ['stat' => $stat, 'count' => $count, 't' => $timestamp ?: time()];

        return $this;
    }

    public function value($stat, $value, $timestamp = null)
    {
        $this->buffer[] = ['stat' => $stat, 'value' => $value, 't' => $timestamp ?: time()];

        return $this;
    }

    public function getStatList()
    {
        $url = sprintf('https://www.stathat.com/x/%s/statlist', $this->accessToken);
        $response = $this->client->get($url);
        return $response->json();
    }

    public function getStat($name)
    {
        $url = sprintf('https://www.stathat.com/x/%s/stat', $this->accessToken);
        $response = $this->client->get($url, ['query' => ['name' => $name]]);
        return $response->json();
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

            $response = $this->client->post('http://api.stathat.com/ez', [
                'json' => $params
            ]);

            if ($response->getStatusCode() === '200') {
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
