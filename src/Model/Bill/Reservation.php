<?php

/*
 * This file is part of the vseth-semesterly-reports project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Model\Bill;

class Reservation
{
    /**
     * @var \DateTime
     */
    private $start;

    /**
     * @var \DateTime
     */
    private $end;

    /**
     * @var string
     */
    private $room;

    /**
     * @var int
     */
    private $pricePerHour;

    /**
     * @var int
     */
    private $total;

    public function getStart(): \DateTime
    {
        return $this->start;
    }

    public function setStart(\DateTime $start): void
    {
        $this->start = $start;
    }

    public function getEnd(): \DateTime
    {
        return $this->end;
    }

    public function setEnd(\DateTime $end): void
    {
        $this->end = $end;
    }

    public function getRoom(): string
    {
        return $this->room;
    }

    public function setRoom(string $room): void
    {
        $this->room = $room;
    }

    public function getPricePerHour(): int
    {
        return $this->pricePerHour;
    }

    public function setPricePerHour(int $pricePerHour): void
    {
        $this->pricePerHour = $pricePerHour;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function setTotal(int $total): void
    {
        $this->total = $total;
    }
}
