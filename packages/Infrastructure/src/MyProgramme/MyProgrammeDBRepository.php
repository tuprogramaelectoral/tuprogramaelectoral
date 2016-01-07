<?php

namespace TPE\Infrastructure\MyProgramme;

use Doctrine\DBAL\Types\Type;
use Ramsey\Uuid\Uuid;
use TPE\Domain\MyProgramme\MyProgramme;
use TPE\Domain\MyProgramme\MyProgrammeRepository;
use TPE\Infrastructure\Data\DBRepository;


class MyProgrammeDBRepository extends DBRepository implements MyProgrammeRepository
{
    const EXPIRATION_WINDOW = '-48 hours';


    public function createNew()
    {
        return new MyProgramme([]);
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

    public function interestsExist(array $interests)
    {
        return $this->_em
            ->createQueryBuilder()
            ->select('count(a.id)')
            ->from('TPE\Domain\Field\Field', 'a')
            ->where('a.id IN (:interests)')
            ->setParameter('interests', $interests)
            ->getQuery()
            ->getSingleScalarResult() == count($interests);
    }

    public function policiesExist(array $policies)
    {
        return $this->_em
            ->createQueryBuilder()
            ->select('count(p.id)')
            ->from('TPE\Domain\Party\Policy', 'p')
            ->where('p.id IN (:policies)')
            ->setParameter('policies', $policies)
            ->getQuery()
            ->getSingleScalarResult() == count($policies);
    }
}
