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
 * StatHatJsonApiInterface exposes the basic features of the StatHat JSON API
 * for stat retrieval.
 *
 * @author Jose Prado <cowlby@me.com>
 */
interface StatHatJsonInterface
{
    /**
     * Sets the Access Token to use for requesting stats.
     *
     * @param string $accessToken
     */
    public function setAccessToken($accessToken);

    /**
     * Gets the list of Stats available.
     *
     * @return array
     */
    public function getStatList();

    /**
     * Updates a value tracker.
     *
     * @param string $name The stat name to retrieve.
     *
     * @return array
     */
    public function getStat($name);
}
