<?php

/*
 * This file is part of the vseth-semesterly-reports project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Enum;

use App\Enum\Base\BaseEnum;

class PaymentRemainderStatusType extends BaseEnum
{
    const NONE = 0;
    const SEEN = 1;
    const PAYMENT_STARTED = 2;
    const PAYMENT_SUCCESSFUL = 3;
}
