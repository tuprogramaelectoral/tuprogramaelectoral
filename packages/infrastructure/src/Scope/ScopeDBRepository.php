<?php

namespace TPE\Infrastructure\Scope;

use TPE\Domain\Scope\Scope;
use TPE\Infrastructure\Data\DBRepository;


class ScopeDBRepository extends DBRepository
{
    /**
     * @param $id
     * @return Scope|null
     */
    public function findScopeWithPoliciesById($id)
    {
        return $this->_em->createQueryBuilder()
            ->select('f, p, pf, pp')
            ->from('TPE\Domain\Scope\Scope', 'f')
            ->leftJoin('f.policies', 'p')
            ->leftJoin('p.scope', 'pf')
            ->leftJoin('p.party', 'pp')
            ->where('f.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
