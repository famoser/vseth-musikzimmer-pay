<?php

/*
 * This file is part of the vseth-semesterly-reports project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use App\Entity\Base\BaseEntity;
use App\Entity\Traits\IdTrait;
use App\Enum\PaymentRemainderStatusType;
use App\Enum\UserCategoryType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * an event determines how the questionnaire looks like.
 *
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 */
class User extends BaseEntity
{
    use IdTrait;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $givenName;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $familyName;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $phone;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $category = UserCategoryType::STUDENTS;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $discount = 0;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $discountDescription;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastPayedPeriodicFeeEnd;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $amountOwned;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $amountOwnedWithFees;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $invoiceHash;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $paymentRemainderStatus = PaymentRemainderStatusType::NONE;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $paymentRemainderStatusAt;

    /**
     * @var PaymentRemainder|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\PaymentRemainder", inversedBy="users")
     */
    private $paymentRemainder;

    /**
     * @var Reservation[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Reservation", mappedBy="user")
     */
    private $reservations;
}
