<?php

/*
 * This file is part of the vseth-semesterly-reports project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Interfaces;

use App\Model\Bill;
use App\Model\PaymentInfo;

interface PaymentServiceInterface
{
    /**
     * @throws \Payrexx\PayrexxException
     *
     * @return PaymentInfo
     */
    public function startPayment(Bill $bill);

    /**
     * @throws \Payrexx\PayrexxException
     *
     * @return bool
     */
    public function paymentSuccessful(PaymentInfo $paymentInfo);

    /**
     * @throws \Payrexx\PayrexxException
     *
     * @return void
     */
    public function closePayment(PaymentInfo $paymentInfo);
}
