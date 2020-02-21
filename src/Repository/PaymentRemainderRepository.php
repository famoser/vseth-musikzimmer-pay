<?php

/*
 * This file is part of the vseth-semesterly-reports project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class PaymentRemainderRepository extends EntityRepository
{
    public function findActive()
    {
        return $this->findOneBy([], ['createdAt' => 'DESC']);
    }
}
