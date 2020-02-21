<?php

/*
 * This file is part of the vseth-semesterly-reports project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service;

use App\Entity\User;
use App\Model\Bill;
use App\Service\Interfaces\BillServiceInterface;
use App\Service\Interfaces\SettingsServiceInterface;

class BillService implements BillServiceInterface
{
    /**
     * @var SettingsServiceInterface
     */
    private $settingService;

    /**
     * BillService constructor.
     *
     * @param $settingService
     */
    public function __construct($settingService)
    {
        $this->settingService = $settingService;
    }

    public function createBill(User $user)
    {
        $bill = new Bill();

        $paymentPrefix = $this->settingService->get()->getPaymentPrefix();
        $bill->setId($paymentPrefix . '-' . $user->getId() . '-' . $user->getPaymentRemainder()->getId());
        $bill->setRecipient($user->createRecipient());

        return $bill;
    }
}
