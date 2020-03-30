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

use App\Entity\User;
use App\Model\Bill;

interface UserPaymentServiceInterface
{
    public function closeInvoice(User $user);

    public function sendPaymentRemainder(User $user);

    public function refreshPaymentStatus(User $user);

    public function startPayment(User $user, Bill $bill, string $url);
}
