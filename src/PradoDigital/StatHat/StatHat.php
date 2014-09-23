<?php

namespace PradoDigital\StatHat;

use GuzzleHttp\ClientInterface;

class StatHat
{
    private $client;
    private $accessToken;

    public function __construct(ClientInterface $client, $accessToken)
    {
        $this->client = $client;
        $this->accessToken = $accessToken;
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
}
