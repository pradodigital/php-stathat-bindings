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
 * StatHatClassicInterface exposes the features of the StatHat Classic API via
 * the count and value methods.
 *
 * @author Jose Prado <cowlby@me.com>
 */
interface StatHatClassicInterface
{
    /**
     * Stores a user key to use when POSTing stats.
     *
     * @param string $userKey
     */
    public function setUserKey($userKey);

    /**
     * Updates a counter stat.
     *
     * @param string $statKey   The unique stat key
     * @param int    $count     The number to count, defaults to 1
     * @param int    $timestamp Optional timestamp, defaults to time()
     *
     * @return StatHatClassicInterface
     */
    public function count($stat, $count = 1, $timestamp = null);

    /**
     * Updates a value tracker.
     *
     * @param string $statKey   The unique stat key
     * @param int    $value     The value to track
     * @param int    $timestamp Optional timestamp, defaults to time()
     *
     * @return StatHatClassicInterface
     */
    public function value($stat, $value, $timestamp = null);
}
