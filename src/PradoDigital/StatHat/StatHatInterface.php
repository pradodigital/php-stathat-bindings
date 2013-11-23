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
     * @param string $stat The unique stat name
     * @param int $count   The number to count
     * @param int $t       Optional timestamp, defaults to time()
     *
     * @return StatHatInterface
     */
    public function count($stat, $count, $t = null);

    /**
     * Updates a value tracker.
     *
     * @param string $stat The unique stat name
     * @param int $count   The value to track
     * @param int $t       Optional timestamp, defaults to time()
     *
     * @return StatHatInterface
     */
    public function value($stat, $value, $t = null);
}
