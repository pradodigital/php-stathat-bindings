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
 * Simple HTTP Client interface  for POSTing messages to the StatHat api.
 *
 * @author Jose Prado <cowlby@me.com>
 */
interface HttpClientInterface
{
    const STATHAT_HOST = 'api.stathat.com';
    const STATHAT_PORT = 443;
    const USER_AGENT = 'PHP StatHat Bindings/1.x (+https://github.com/pradodigital/php-stathat-bindings)';

    /**
     * POSTs an array of parameters to the StatHat API.
     *
     * @param string $path The path to POST to
     * @param array $params The parameters to POST
     *
     * @return boolean Whether or not the request was succesful
     */
    public function post($path, array $params);
}
