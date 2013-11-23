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
 * AsyncHttpClient POSTs a request asynchronously in one call in JSON format.
 *
 * @author Jose Prado <cowlby@me.com>
 */
class AsyncHttpClient implements HttpClientInterface
{
    /**
     * Asynchronous POST request by closing the conneciton immediatelly after
     * sending.
     *
     * {@inheritdoc}
     */
    public function post($path, array $params)
    {
        $json = json_encode($params);

        $request  = "POST ".$path." HTTP/1.1\r\n";
        $request .= "Host: ".HttpClientInterface::STATHAT_HOST."\r\n";
        $request .= "User-Agent: ".HttpClientInterface::USER_AGENT."\r\n";
        $request .= "Content-Type: application/json\r\n";
        $request .= "Content-Length: ".strlen($json)."\r\n";
        $request .= "Connection: Close\r\n\r\n";
        $request .= $json;

        $fp = fsockopen('ssl://'.HttpClientInterface::STATHAT_HOST, HttpClientInterface::STATHAT_PORT);
        fwrite($fp, $request);
        return fclose($fp);
    }
}
