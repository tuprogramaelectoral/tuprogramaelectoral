<?php

namespace TPE\Infrastructure\Scope;

use TPE\Domain\Scope\Scope;
use TPE\Infrastructure\Data\DBRepository;


class ScopeDBRepository extends DBRepository
{
    /**
     * @param int $edition
     * @param string $scope
     * @return null|Scope
     */
    public function findScopeWithPolicies($edition, $scope)
    {
        return $this->_em->createQueryBuilder()
            ->select('s, p, pf, pp')
            ->from('TPE\Domain\Scope\Scope', 's')
            ->leftJoin('s.policies', 'p')
            ->leftJoin('s.election', 'e')
            ->leftJoin('p.scope', 'pf')
            ->leftJoin('p.party', 'pp')
            ->where('e.id = :edition')
            ->andWhere('s.scope = :scope')
            ->setParameter('scope', $scope)
            ->setParameter('edition', $edition)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $edition
     * @return Scope[]
     */
    public function findScopesByEdition($edition)
    {
        return $this->_em->createQueryBuilder()
            ->select('s')
            ->from('TPE\Domain\Scope\Scope', 's')
            ->leftJoin('s.election', 'e')
            ->where('e.id = :id')
            ->setParameter('id', $edition)
            ->getQuery()
            ->getResult();
    }
}
