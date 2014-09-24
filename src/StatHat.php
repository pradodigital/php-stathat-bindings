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

class StatHat implements StatHatEzInterface, StatHatJsonInterface
{
    private $buffer;
    private $client;
    private $accessToken;
    private $ezKey;

    public function __construct(ClientInterface $client, $accessToken = null, $ezKey = null)
    {
        $this->client = $client;

        if ($accessToken !== null) {
            $this->setAccessToken($accessToken);
        }

        if ($ezKey !== null) {
            $this->setEzKey($ezKey);
        }

        register_shutdown_function([$this, 'postBatch']);

        $this->resetBuffer();
    }

    /**
     * @see \PradoDigital\StatHat\StatHatJsonInterface::setAccessToken()
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
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
    public function ezCount($stat, $count = 1, $timestamp = null)
    {
        $this->buffer[$this->ezKey][] = ['stat' => $stat, 'count' => $count, 't' => $timestamp ?: time()];

        return $this;
    }

    /**
     * @see \PradoDigital\StatHat\StatHatEzInterface::ezValue()
     */
    public function ezValue($stat, $value, $timestamp = null)
    {
        $this->buffer[$this->ezKey][] = ['stat' => $stat, 'value' => $value, 't' => $timestamp ?: time()];

        return $this;
    }

    /**
     * @see \PradoDigital\StatHat\StatHatJsonInterface::getStatList()
     */
    public function getStatList()
    {
        $url = ['https://www.stathat.com/x/{accessToken}/statlist', [
            'accessToken' => $this->accessToken
        ]];

        $request = $this->client->createRequest('GET', $url);
        $response = $this->client->send($request);

        return $response->json();
    }

    /**
     * @see \PradoDigital\StatHat\StatHatJsonInterface::getStat()
     */
    public function getStat($name)
    {
        $url = ['https://www.stathat.com/x/{accessToken}/stat', [
            'accessToken' => $this->accessToken
        ]];

        $request = $this->client->createRequest('GET', $url, [
            'query' => ['name' => $name]
        ]);

        $response = $this->client->send($request);

        return $response->json();
    }

    /**
     * @see \PradoDigital\StatHat\StatHatJsonInterface::getDatasets()
     */
    public function getDatasets($statId, $timeframe, $start = null)
    {
        $url = ['https://www.stathat.com/x/{accessToken}/data/{statId}', [
            'accessToken' => $this->accessToken,
            'statId' => $statId
        ]];

        $request = $this->client->createRequest('GET', $url, [
            'query' => ['t' => $timeframe, 'start' => $start]
        ]);

        $response = $this->client->send($request);

        return $response->json();
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

                $response = $this->client->post('https://api.stathat.com/ez', [
                    'json' => $stats
                ]);

                if ($response->getStatusCode() === '200') {
                    $this->clearBuffer($ezKey);
                }
            }
        }
    }

    /**
     * Empties the buffer.
     *
     * @return StatHat
     */
    private function resetBuffer()
    {
        $this->buffer = [];

        return $this;
    }
}
