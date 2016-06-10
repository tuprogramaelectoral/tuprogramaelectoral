<?php

namespace TPE\Infrastructure\Party;

use TPE\Domain\Party\Party;
use TPE\Infrastructure\Data\DBRepository;


class PartyDBRepository extends DBRepository
{
    /**
     * @param $edition
     * @return Party[]
     */
    public function findPartiesByEdition($edition)
    {
        return $this->_em->createQueryBuilder()
            ->select('p')
            ->from(Party::class, 'p')
            ->leftJoin('p.election', 'e')
            ->where('e.id = :id')
            ->setParameter('id', $edition)
            ->getQuery()
            ->getResult();
    }
}
