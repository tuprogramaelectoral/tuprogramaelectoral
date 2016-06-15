<?php

namespace TPE\Infrastructure\Party;

use TPE\Domain\Party\PolicyRepository;
use TPE\Domain\Party\Policy;
use TPE\Domain\Scope\Scope;
use TPE\Infrastructure\Data\DBRepository;


class PolicyDBRepository extends DBRepository implements PolicyRepository
{
    /**
     * @param integer $edition
     * @param string $scope
     * @param string $party
     * @return Policy[]
     */
    public function findPolicyByEditionScopeAndParty($edition, $scope, $party)
    {
        return $this->_em->createQueryBuilder()
            ->select('po')
            ->from(Policy::class, 'po')
            ->leftJoin('po.scope', 's')
            ->leftJoin('po.party', 'pa')
            ->where('s.scope = :scope AND s.election = :edition')
            ->andWhere('pa.party = :party AND pa.election = :edition')
            ->setParameters([
                'scope' => $scope,
                'party' => $party,
                'edition' => $edition
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }
}
