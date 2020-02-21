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
use App\Model\Bill\Recipient;
use App\Model\PaymentInfo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

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
    private $authenticationCode;

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
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $invoiceLink;

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

    public function generateAuthenticationCode(): void
    {
        $this->authenticationCode = Uuid::uuid4();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getGivenName(): string
    {
        return $this->givenName;
    }

    public function setGivenName(string $givenName): void
    {
        $this->givenName = $givenName;
    }

    public function getFamilyName(): string
    {
        return $this->familyName;
    }

    public function setFamilyName(string $familyName): void
    {
        $this->familyName = $familyName;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function getCategory(): int
    {
        return $this->category;
    }

    public function setCategory(int $category): void
    {
        $this->category = $category;
    }

    public function getDiscount(): int
    {
        return $this->discount;
    }

    public function setDiscount(int $discount): void
    {
        $this->discount = $discount;
    }

    public function getDiscountDescription(): ?string
    {
        return $this->discountDescription;
    }

    public function setDiscountDescription(?string $discountDescription): void
    {
        $this->discountDescription = $discountDescription;
    }

    public function getLastPayedPeriodicFeeEnd(): ?\DateTime
    {
        return $this->lastPayedPeriodicFeeEnd;
    }

    public function setLastPayedPeriodicFeeEnd(?\DateTime $lastPayedPeriodicFeeEnd): void
    {
        $this->lastPayedPeriodicFeeEnd = $lastPayedPeriodicFeeEnd;
    }

    public function getAmountOwned(): int
    {
        return $this->amountOwned;
    }

    public function setAmountOwned(int $amountOwned): void
    {
        $this->amountOwned = $amountOwned;
    }

    public function getAmountOwnedWithFees(): int
    {
        return $this->amountOwnedWithFees;
    }

    public function setAmountOwnedWithFees(int $amountOwnedWithFees): void
    {
        $this->amountOwnedWithFees = $amountOwnedWithFees;
    }

    public function getInvoiceHash(): ?string
    {
        return $this->invoiceHash;
    }

    public function setInvoiceHash(?string $invoiceHash): void
    {
        $this->invoiceHash = $invoiceHash;
    }

    public function getInvoiceLink(): ?string
    {
        return $this->invoiceLink;
    }

    public function setInvoiceLink(?string $invoiceLink): void
    {
        $this->invoiceLink = $invoiceLink;
    }

    public function getPaymentRemainderStatus(): int
    {
        return $this->paymentRemainderStatus;
    }

    public function setPaymentRemainderStatus(int $paymentRemainderStatus): void
    {
        $this->paymentRemainderStatus = $paymentRemainderStatus;
        $this->paymentRemainderStatusAt = new \DateTime();
    }

    public function getPaymentRemainderStatusAt(): ?\DateTime
    {
        return $this->paymentRemainderStatusAt;
    }

    public function getPaymentRemainder(): ?PaymentRemainder
    {
        return $this->paymentRemainder;
    }

    public function setPaymentRemainder(?PaymentRemainder $paymentRemainder): void
    {
        $this->paymentRemainder = $paymentRemainder;
    }

    /**
     * @return Reservation[]|ArrayCollection
     */
    public function getReservations()
    {
        return $this->reservations;
    }

    /**
     * @param Reservation[]|ArrayCollection $reservations
     */
    public function setReservations($reservations): void
    {
        $this->reservations = $reservations;
    }

    public function writePaymentInfo(\App\Model\PaymentInfo $paymentInfo)
    {
        $this->invoiceHash = $paymentInfo->getInvoiceHash();
        $this->invoiceLink = $paymentInfo->getInvoiceLink();
    }

    /**
     * @return PaymentInfo
     */
    public function getPaymentInfo()
    {
        $paymentInfo = new PaymentInfo();

        $paymentInfo->setInvoiceHash($this->invoiceHash);
        $paymentInfo->setInvoiceLink($this->invoiceLink);

        return $paymentInfo;
    }

    public function clearPaymentInfo()
    {
        $this->invoiceLink = null;
        $this->invoiceHash = null;
    }

    /**
     * @return Recipient
     */
    public function createRecipient()
    {
        $recipient = new Recipient();
        $recipient->setEmail($this->email);

        $recipient->setGivenName($this->givenName);
        $recipient->setFamilyName($this->familyName);

        $addressLines = explode("\n", $this->address);
        if (\count($addressLines) === 3) {
            $recipient->setAddressLine2($addressLines[1]);
        }

        if (\count($addressLines) > 0) {
            $recipient->setAddressLine1($addressLines[0]);
        }

        if (\count($addressLines) > 1) {
            $recipient->setCity($addressLines[\count($addressLines) - 1]);
        }

        return $recipient;
    }
}
