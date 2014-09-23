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
 * AsyncHttpClient POSTs a request asynchronously in one call in JSON format.
 *
 * @author Jose Prado <cowlby@me.com>
 */
class AsyncHttpClient implements HttpClientInterface
{
    /**
     * Asynchronous POST request by closing the conneciton immediately after
     * sending.
     *
     * {@inheritdoc}
     *
     * @param string $path  The path to POST to
     * @param array $params The parameters to POST
     *
     * @return boolean Whether or not the request was succesful
     */
    public function post($path, array $params, $contentType = HttpClientInterface::DEFAULT_CONTENT_TYPE)
    {
        if (!in_array($contentType, ['application/json', 'application/x-www-form-urlencoded'])) {
            throw new \RuntimeException('Invalid Content-Type set in HTTP Client.');
        }

        $body = $this->encodeParams($params, $contentType);

        $request  = "POST ".$path." HTTP/1.1\r\n";
        $request .= "Host: ".HttpClientInterface::STATHAT_HOST."\r\n";
        $request .= "User-Agent: ".HttpClientInterface::USER_AGENT."\r\n";
        $request .= "Content-Type: ".$contentType."\r\n";
        $request .= "Content-Length: ".strlen($body)."\r\n";
        $request .= "Connection: Close\r\n\r\n";
        $request .= $body;

        $socket = fsockopen('ssl://'.HttpClientInterface::STATHAT_HOST, HttpClientInterface::STATHAT_PORT);
        fwrite($socket, $request);
        return fclose($socket);
    }

    /**
     * Encodes the parameters into the right format based on the content type.
     *
     * @param array $params
     * @param string $contentType
     *
     * @return string
     */
    private function encodeParams($params, $contentType)
    {
        switch ($contentType) {

            case 'application/json':
                $encodedParams = json_encode($params);
                break;

            default:
                $encodedParams = http_build_query($params);
                break;
        }

        return $encodedParams;
    }
}
