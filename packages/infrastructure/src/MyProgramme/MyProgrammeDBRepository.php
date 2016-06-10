<?php

namespace TPE\Infrastructure\MyProgramme;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query\Expr;
use Ramsey\Uuid\Uuid;
use TPE\Domain\Election\Election;
use TPE\Domain\MyProgramme\MyProgramme;
use TPE\Domain\MyProgramme\MyProgrammeRepository;
use TPE\Domain\Party\Party;
use TPE\Domain\Party\Policy;
use TPE\Domain\Scope\Scope;
use TPE\Infrastructure\Data\DBRepository;


class MyProgrammeDBRepository extends DBRepository implements MyProgrammeRepository
{
    const EXPIRATION_WINDOW = '-48 hours';


    public function createNew()
    {
        return new MyProgramme([], 1);
    }

    public function findOneBy(array $criteria)
    {
        if (isset($criteria['id']) && !Uuid::isValid($criteria['id'])) {
            return null;
        }

        return parent::findOneBy($criteria);
    }

    public function findNotExpiredById($id)
    {
        if (Uuid::isValid($id)) {
            return $this->_em->createQueryBuilder()
                ->select('mp')
                ->from('TPE\Domain\MyProgramme\MyProgramme', 'mp')
                ->where('mp.id = :id')
                ->andWhere('mp.public = true OR (mp.public = false AND mp.lastModification > :expirationDate)')
                ->setParameter('id', $id)
                ->setParameter('expirationDate', new \DateTime('-48 hours'))
                ->getQuery()
                ->getOneOrNullResult();
        }

        return null;
    }

    public function interestsExist($edition, array $interests)
    {
        $query = $this->_em
            ->createQueryBuilder()
            ->select('count(s.id)')
            ->from(Scope::class, 's')
            ->leftJoin('s.election', 'e')
            ->where('e.id = :edition')
            ->andWhere('s.scope IN (:interests)')
            ->setParameter('interests', $interests)
            ->setParameter('edition', $edition)
            ->getQuery();

        return $query->getSingleScalarResult() == count($interests);
    }

    public function policiesExist($edition, array $policies)
    {
        $builder = $this->_em
            ->createQueryBuilder()
            ->select('count(po.id)')
            ->from(Policy::class, 'po')
            ->leftJoin(Party::class, 'pa', Expr\Join::WITH, 'po.party = pa.id')
            ->leftJoin(Scope::class, 's', Expr\Join::WITH, 'po.scope = s.id')
            ->leftJoin(Election::class, 'e', Expr\Join::WITH, 'pa.election = e.id AND s.election = e.id');

        $i = 0;
        foreach ($policies as $scope => $party) {
            if (!empty($party)) {
                $builder
                    ->Orwhere("e.id = :election{$i} AND pa.party = :party{$i} AND s.scope = :scope{$i}")
                    ->setParameter("election{$i}", $edition)
                    ->setParameter("party{$i}", $party)
                    ->setParameter("scope{$i}", $scope);
                $i++;
            }
        }

        if ($i === 0) {
            return true;
        }

        return $builder->getQuery()->getSingleScalarResult() == $i;
    }
}
