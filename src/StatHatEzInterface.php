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
 * StatHatEzInterface exposes the features of the StatHat EZ API via the count
 * and value methods.
 *
 * @author Jose Prado <cowlby@me.com>
 */
interface StatHatEzInterface
{
    /**
     * Stores an EZ key to use when POSTing stats.
     *
     * @param string $ezKey
     */
    public function setEzKey($ezKey);

    /**
     * Updates a counter stat.
     *
     * @param string $statName  The unique stat name
     * @param int    $count     The number to count, defaults to 1
     * @param int    $timestamp Optional timestamp, defaults to time()
     *
     * @return StatHatEzInterface
     */
    public function ezCount($statName, $count = 1, $timestamp = null);

    /**
     * Updates a value tracker.
     *
     * @param string $statName  The unique stat name
     * @param int    $value     The value to track
     * @param int    $timestamp Optional timestamp, defaults to time()
     *
     * @return StatHatEzInterface
     */
    public function ezValue($statName, $value, $timestamp = null);
}
