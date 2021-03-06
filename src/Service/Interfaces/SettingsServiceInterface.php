<?php

/*
 * This file is part of the vseth-musikzimmer-pay project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Interfaces;

use App\Entity\Setting;

interface SettingsServiceInterface
{
    /**
     * @return Setting
     */
    public function get();

    public function set(Setting $setting);
}
