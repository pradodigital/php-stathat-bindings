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
 * StatHatInterface exposes the basic features of the StatHat API via the
 * count and value methods.
 *
 * @author Jose Prado <cowlby@me.com>
 */
interface StatHatInterface
{
    /**
     * Updates a counter stat.
     *
     * @param string $stat   The unique stat name or key
     * @param int $count     The number to count, defaults to 1
     * @param int $timestamp Optional timestamp, defaults to time()
     *
     * @return StatHatInterface
     */
    public function count($stat, $count = 1, $timestamp = null);

    /**
     * Updates a value tracker.
     *
     * @param string $stat   The unique stat name or key
     * @param int $value     The value to track
     * @param int $timestamp Optional timestamp, defaults to time()
     *
     * @return StatHatInterface
     */
    public function value($stat, $value, $timestamp = null);
}
